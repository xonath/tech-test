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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('latitude');
            $table->float('longitude');
            $table->boolean('is_open')->default(true);
            $table->enum('store_type', ['takeaway', 'shop', 'restaurant']);
            $table->decimal('max_delivery_distance', 5, 2);
            $table->timestamps();

            $table->foreignId('postcode_id')->constrained('postcodes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
