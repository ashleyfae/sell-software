<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('legacy_mappings', function (Blueprint $table) {
            $table->id();
            $table->morphs('mappable');
            $table->text('source');
            $table->unsignedBigInteger('source_id');
            $table->json('source_data')->nullable();
            $table->timestamps();

            $table->unique(['source', 'source_id', 'mappable_type']);
            $table->unique(['mappable_id', 'mappable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legacy_mappings');
    }
};
