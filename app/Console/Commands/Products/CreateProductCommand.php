<?php

namespace App\Console\Commands\Products;

use App\Models\Product;
use Illuminate\Console\Command;

class CreateProductCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:create
                            {name : Name of the product}
                            {stripe_id : Stripe product ID}
                            {--git_repo= : Git repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new product';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $product = Product::create([
            'name' => $this->argument('name'),
            'stripe_id' => $this->argument('stripe_id'),
            'git_repo' => $this->option('git_repo') ?: null,
        ]);

        $this->line("Successfully created product #{$product->id}: {$product->name}");
        dump($product->toArray());
    }
}
