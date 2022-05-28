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
        Schema::create('site_activations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\License::class)->constrained()->cascadeOnDelete();
            $table->text('domain');
            $table->boolean('is_local');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['license_id', 'is_local']);
            $table->index(['license_id', 'domain', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_activations');
    }
};
