<?php

use App\Enums\LicenseStatus;
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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\User::class)->constrained()->cascadeOnDelete();
            $table->uuid('license_key')->unique();
            $table->string('status', 40)->default(LicenseStatus::Active->value);
            $table->foreignIdFor(\App\Models\Product::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\ProductPrice::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\OrderItem::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('activation_limit')->nullable();
            $table->dateTime('expires_at')->nullable()->default(null);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('licenses');
    }
};
