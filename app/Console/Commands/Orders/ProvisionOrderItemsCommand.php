<?php

namespace App\Console\Commands\Orders;

use App\Enums\OrderStatus;
use App\Jobs\ProvisionOrderItemJob;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class ProvisionOrderItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:provision';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provisions order items that have not been provisioned.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = $this->getItemsForProvisioning();
        if ($items->isEmpty()) {
            $this->line('No order items found.');
            return;
        }

        $this->line("Found {$items->count()} items that need provisioning.");

        foreach($items as $item) {
            if ($item->needsProvisioning()) {
                $this->line("Dispatching provisioner for order item #{$item->id}");
                ProvisionOrderItemJob::dispatch($item);
            }
        }
    }

    /**
     * @return Collection&OrderItem[]
     */
    protected function getItemsForProvisioning() : Collection
    {
        return OrderItem::query()
            ->where('status', OrderStatus::Complete->value)
            ->whereNull('provisioned_at')
            ->get();
    }
}
