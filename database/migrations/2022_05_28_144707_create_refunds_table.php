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
            $table->text('stripe_refund_id')->nullable()->unique();
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
        Schema::dropIfExists('refunds');
    }
};
