<?php

namespace App\Console\Commands\Prices;

use App\Enums\Currency;
use App\Enums\PeriodUnit;
use App\Helpers\Money;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Console\Command;
use Stripe\StripeClient;

class CreatePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:create
                            {name : Name of the price}
                            {stripe_id : ID of the price in Stripe}
                            {--license_period=1 : License period}
                            {--license_period_unit='.PeriodUnit::Year->value.' : License period unit}
                            {--activation_limit= : If omitted, no limit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new product price.';

    public function __construct(protected StripeClient $stripeClient)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $stripePrice = $this->stripeClient->prices->retrieve($this->argument('stripe_id'));

        dump($stripePrice->toArray());

        if (! $this->confirm('Is this the correct price?')) {
            $this->info('Creation stopped.');

            return;
        }

        $product = Product::where('stripe_id', $stripePrice->product)->firstOrFail();

        /** @var ProductPrice $price */
        $price = $product->prices()->create([
            'name'                => $this->argument('name'),
            'price'               => new Money(Currency::from($stripePrice->currency), $stripePrice->unit_amount),
            'license_period'      => (int) $this->option('license_period'),
            'license_period_unit' => PeriodUnit::from($this->option('license_period_unit')),
            'activation_limit'    => $this->option('activation_limit') ?: null,
            'stripe_id'           => $stripePrice->id,
        ]);

        $this->info("Successfully created price #{$price->id}");
        dump($price->toArray());
    }
}
