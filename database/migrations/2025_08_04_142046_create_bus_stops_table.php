<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // We drop the table if it exists to ensure a clean slate
        Schema::dropIfExists('bus_stops');

        Schema::create('bus_stops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Adding the location columns DIRECTLY here
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_stops');
    }
};