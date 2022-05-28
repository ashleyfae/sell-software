<?php

use App\Enums\OrderItemType;
use App\Enums\OrderStatus;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('object');
            $table->foreignIdFor(\App\Models\Product::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\ProductPrice::class)->nullable()->constrained()->nullOnDelete();
            $table->text('product_name');
            $table->string('status', 20)->default(OrderStatus::Pending->value);
            $table->string('type', 60)->default(OrderItemType::New->value)->index();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'product_price_id']);
            $table->index(['status', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
