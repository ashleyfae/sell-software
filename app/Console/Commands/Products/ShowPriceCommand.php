<?php

namespace App\Console\Commands\Products;

use App\Models\ProductPrice;
use Illuminate\Console\Command;

class ShowPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:show {stripe_id : ID of the Stripe price}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Displays information about a specific price.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $price = ProductPrice::query()
            ->where('stripe_id', $this->argument('stripe_id'))
            ->firstOrFail();

        dump($price->toArray());

        $this->line('Purchase Link: '.route('buy', $price));
    }
}
