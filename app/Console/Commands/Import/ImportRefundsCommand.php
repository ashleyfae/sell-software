<?php

namespace App\Console\Commands\Import;

use App\Enums\PaymentGateway;
use App\Imports\DataObjects\LegacyOrder;
use App\Imports\DataObjects\LegacyRefund;
use App\Imports\Repositories\MappingRepository;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ImportRefundsCommand extends ImportOrdersCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:refunds {--dry-run} {--max=} {--legacy-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports legacy refunds';

    protected string $orderType = 'refund';

    public function __construct(MappingRepository $mappingRepository)
    {
        parent::__construct($mappingRepository);

        $this->dataType = new Refund();
    }

    protected function makeItemObject(object $itemRow): LegacyRefund
    {
        return new LegacyRefund(
            id: $itemRow->id,
            displayOrderNumber: $itemRow->order_number ?: null,
            orderId: $this->getNewOrderId($itemRow->parent),
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
            gatewayTransactionId: $this->getGatewayTransactionId($itemRow),
            items: $this->makeOrderItems($itemRow)
        );
    }

    protected function getNewOrderId(int $legacyOrderId) : int
    {
        return $this->mappingRepository->getSingleMappingQuery(
            source: Config::get('imports.currentSource'),
            sourceId: $legacyOrderId,
            dataType: new Order()
        )
            ->firstOrFail()->mappable_id;
    }

    /**
     * @param  LegacyRefund  $item
     *
     * @return Refund
     */
    protected function createOrder(object $item): Model
    {
        $refund = new Refund();
        $refund->custom_id = $item->displayOrderNumber ?: null;
        $refund->order_id = $item->orderId;
        $refund->user_id = $item->userId;
        $refund->status = $item->orderStatus;
        $refund->gateway = $item->gateway;
        $refund->ip = $item->ip;
        $refund->subtotal = $item->subtotal;
        $refund->discount = $item->discount;
        $refund->tax = $item->tax;
        $refund->total = $item->total;
        $refund->currency = $item->currency;
        $refund->rate = $item->rate;
        $refund->completed_at = $item->dateCompleted;
        $refund->gateway_transaction_id = $item->gatewayTransactionId;

        if (! $this->isDryRun()) {
            $refund->save();
        }

        $this->line('-- Refund: '.$refund->toJson());

        return $refund;
    }
}
