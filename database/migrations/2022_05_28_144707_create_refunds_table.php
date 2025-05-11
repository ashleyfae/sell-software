<?php

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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->text('custom_id')->nullable()->default(null)->index();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Order::class)->constrained()->nullOnDelete();
            $table->numericMorphs('object');
            $table->string('status', 20)->default(OrderStatus::Pending->value)->index();
            $table->string('gateway', 100)->default(\App\Enums\PaymentGateway::Manual->value);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->char('currency', 3);
            $table->decimal('rate', 10, 5)->default(1);
            $table->dateTime('completed_at')->nullable()->default(null);
            $table->text('gateway_transaction_id')->nullable()->unique();
            $table->timestamps();
        });

        DB::statement(
            "ALTER TABLE orders ADD COLUMN display_id text GENERATED ALWAYS AS (CASE WHEN custom_id IS NOT NULL THEN custom_id ELSE uuid::text END) STORED"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('refunds');
    }
};
