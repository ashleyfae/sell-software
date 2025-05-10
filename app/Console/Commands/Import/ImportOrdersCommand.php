<?php

namespace App\Console\Commands\Import;

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
use App\Enums\PaymentGateway;
use App\Helpers\Money;
use App\Imports\Database\ImportQuery;
use App\Imports\DataObjects\LegacyOrder;
use App\Imports\DataObjects\LegacyOrderItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class ImportOrdersCommand extends AbstractImportCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:orders {--dry-run} {--max=} {--legacy-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports legacy orders.';

    protected function getItemsToImportQuery(): Builder
    {
        return ImportQuery::make()->table('wp_edd_orders')
            ->when($this->option('legacy-id'), function(Builder $builder) {
                $builder->where('id', $this->option('legacy-id'));
            });
    }

    /**
     * @throws Exception
     */
    protected function makeItemObject(object $itemRow): LegacyOrder
    {
        return new LegacyOrder(
            id: $itemRow->id,
            displayOrderNumber: $itemRow->order_number ?: null,
            userId: $this->convertLegacyCustomerIdToUserId((int) $itemRow->customer_id),
            orderStatus: $this->convertOrderStatus($itemRow->status),
            gateway: PaymentGateway::from($itemRow->gateway ?? 'manual'),
            ip: $itemRow->ip ?: null,
            currency: strtolower($itemRow->currency),
            subtotal: $this->convertFloatMoney($itemRow->subtotal ?? 0),
            discount: $this->convertFloatMoney($itemRow->discount ?? 0),
            tax: $this->convertFloatMoney($itemRow->tax ?? 0),
            total: $this->convertFloatMoney($itemRow->total ?? 0),
            rate: (float) $itemRow->rate ?? 1,
            dateCreated: $itemRow->date_created,
            dateCompleted: $itemRow->date_completed,
            stripeChargeId: $this->getStripeChargeId($itemRow),
            items: $this->makeOrderItems($itemRow)
        );
    }

    protected function convertFloatMoney(string|float $amount) : int
    {
        return (int) round((float) $amount * 100);
    }

    /**
     * @param  int  $legacyCustomerId
     *
     * @return int
     * @throws Exception
     */
    protected function convertLegacyCustomerIdToUserId(int $legacyCustomerId) : int
    {
        return (int) $this->mappingRepository->getSingleMappingQuery(
            source: Config::get('imports.currentSource'),
            sourceId: $legacyCustomerId,
            dataType: new User()
        )
            ->valueOrFail('mappable_id');
    }

    protected function convertOrderStatus(string $legacyStatus) : OrderStatus
    {
        return match($legacyStatus) {
            'complete' => OrderStatus::Complete,
            'partially_refunded' => OrderStatus::PartiallyRefunded,
            'refunded' => OrderStatus::Refunded,
            default => OrderStatus::Pending,
        };
    }

    protected function getStripeChargeId(object $orderRow) : ?string
    {
        if ($orderRow->gateway !== 'stripe') {
            return null;
        }

        $transactionId = ImportQuery::make()->table('wp_edd_order_transactions')
            ->where('object_id', $orderRow->id)
            ->where('object_type', 'order')
            ->value('transaction_id');

        return $transactionId ?? null;
    }

    /**
     * @return LegacyOrderItem[]
     */
    protected function makeOrderItems(object $orderRow) : array
    {
        $convertedItems = [];
        $legacyItemRows = ImportQuery::make()->table('wp_edd_order_items')
            ->where('order_id', $orderRow->id)
            ->get();

        if ($legacyItemRows->isEmpty()) {
            $this->warn("-- No order items found for order #{$orderRow->id}");
            return $convertedItems;
        }

        foreach($legacyItemRows as $legacyItemRow) {
            if ($this->mappingRepository->isBundleProduct($legacyItemRow->product_id)) {
                $convertedItems = array_merge(
                    $convertedItems,
                    $this->makeBundleOrderItems($orderRow, $legacyItemRow)
                );
            } else {
                $convertedItems[] = $this->makeOrderItem($orderRow, $legacyItemRow);
            }
        }

        return $convertedItems;
    }

    protected function makeOrderItem(object $orderRow, object $legacyItemRow) : LegacyOrderItem
    {
        return new LegacyOrderItem(
            id: $legacyItemRow->id,
            productName: $legacyItemRow->product_name,
            productId: $this->mappingRepository->getNewProductIdFromLegacyProductId($legacyItemRow->product_id),
            priceId: $this->mappingRepository->getNewPriceIdFromLegacyProductId(
                legacyProductId: $legacyItemRow->product_id,
                legacyPriceIndex: $legacyItemRow->price_id
            ),
            status: $this->convertOrderStatus($legacyItemRow->status),
            orderItemType: $this->determineOrderItemType($legacyItemRow->id),
            subtotal: $this->convertFloatMoney($legacyItemRow->subtotal ?? 0),
            discount: $this->convertFloatMoney($legacyItemRow->discount ?? 0),
            tax: $this->convertFloatMoney($legacyItemRow->tax ?? 0),
            total: $this->convertFloatMoney($legacyItemRow->total ?? 0),
            dateCreated: $legacyItemRow->date_created
        );
    }

    /**
     * @return LegacyOrderItem[]
     */
    protected function makeBundleOrderItems(object $orderRow, object $legacyItemRow) : array
    {
        return [];
    }

    protected function determineOrderItemType(int $orderItemId) : OrderItemType
    {
        $renewalMeta = ImportQuery::make()->table('wp_edd_order_itemmeta')
            ->where('edd_order_item_id', $orderItemId)
            ->where('meta_key', '_option_is_renewal')
            ->value('meta_value');

        return ! empty($renewalMeta) ? OrderItemType::Renewal : OrderItemType::New;
    }

    protected function itemExists(object $item): bool
    {
        return $this->mappingRepository->hasMapping(
            source: Config::get('imports.currentSource'),
            sourceId: $item->id,
            dataType: new Order()
        );
    }

    /**
     * @param  LegacyOrder  $item
     */
    protected function importItem(object $item): void
    {
        DB::transaction(function() use($item) {
            $order = new Order();
            $order->custom_id = $item->displayOrderNumber ?: null;
            $order->user_id = $item->userId;
            $order->status = $item->orderStatus;
            $order->gateway = $item->gateway;
            $order->ip = $item->ip;
            $order->subtotal = $item->subtotal;
            $order->discount = $item->discount;
            $order->tax = $item->tax;
            $order->total = $item->total;
            $order->currency = $item->currency;
            $order->rate = $item->rate;
            $order->completed_at = $item->dateCompleted;
            $order->save();

            $mapping = $this->makeLegacyMapping($item);
            $this->line('-- Order Mapping: '.$mapping->toJson());
            if (! $this->isDryRun()) {
                $order->legacyMapping()->save($mapping);
            }

            foreach($item->items as $legacyOrderItem) {
                $newOrderItem = new OrderItem();
                $newOrderItem->product_id = $legacyOrderItem->productId;
                $newOrderItem->product_price_id = $legacyOrderItem->priceId;
                $newOrderItem->product_name = $legacyOrderItem->productName;
                $newOrderItem->status = $legacyOrderItem->status;
                $newOrderItem->type = $legacyOrderItem->orderItemType;
                $newOrderItem->subtotal = $legacyOrderItem->subtotal;
                $newOrderItem->discount = $legacyOrderItem->discount;
                $newOrderItem->tax = $legacyOrderItem->tax;
                $newOrderItem->total = $legacyOrderItem->total;
                $newOrderItem->currency = $order->currency;
                $newOrderItem->provisioned_at = ($order->status == OrderStatus::Complete ? $order->completed_at : null);
                $newOrderItem->created_at = $legacyOrderItem->dateCreated;

                $order->orderItems()->save($newOrderItem);

                $mapping = $this->makeLegacyMapping($legacyOrderItem);
                $this->line('-- Item Mapping: '.$mapping->toJson());
                if (! $this->isDryRun()) {
                    $newOrderItem->legacyMapping()->save($mapping);
                }
            }
        });
    }
}
