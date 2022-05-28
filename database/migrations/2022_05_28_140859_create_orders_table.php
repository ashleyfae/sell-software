<?php

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignIdFor(\App\Models\Store::class)->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default(\App\Enums\OrderStatus::Pending->value)->index();
            $table->string('gateway', 100)->default(\App\Enums\PaymentGateway::Manual->value);
            $table->ipAddress('ip')->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->char('currency', 3);
            $table->decimal('rate', 10, 5)->default(1);
            $table->dateTime('completed_at')->nullable()->default(null);
            $table->text('stripe_session_id')->nullable()->unique();
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
        Schema::dropIfExists('orders');
    }
};
