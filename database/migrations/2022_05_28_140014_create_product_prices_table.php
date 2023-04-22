<?php

use App\Enums\PeriodUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignIdFor(\App\Models\Product::class)->index()->constrained()->cascadeOnDelete();
            $table->text('name');
            $table->char('currency', 3)->default('usd');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('license_period')->nullable()->default(1);
            $table->enum('license_period_unit', $this->getValidUnits())->default(PeriodUnit::Year->value);
            $table->unsignedBigInteger('activation_limit')->nullable();
            $table->text('stripe_id')->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_prices');
    }

    protected function getValidUnits(): array
    {
        return array_map(fn(PeriodUnit $unit) => $unit->value, PeriodUnit::cases());
    }
};
