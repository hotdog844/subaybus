<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('routes', function (Blueprint $table) {
        // 1. Add PATH DATA (The blue line)
        if (!Schema::hasColumn('routes', 'path_data')) {
            // We remove 'after' just to be safe
            $table->json('path_data')->nullable(); 
        }
        
        // 2. Add ORIGIN Coordinates
        if (!Schema::hasColumn('routes', 'origin_lat')) {
            $table->decimal('origin_lat', 10, 7)->nullable();
            $table->decimal('origin_lng', 10, 7)->nullable();
        }

        // 3. Add DESTINATION Coordinates
        if (!Schema::hasColumn('routes', 'destination_lat')) {
            $table->decimal('destination_lat', 10, 7)->nullable();
            $table->decimal('destination_lng', 10, 7)->nullable();
        }
    });

    // 4. Ensure STOPS table has coordinates
    if (Schema::hasTable('stops')) {
         Schema::table('stops', function (Blueprint $table) {
             if (!Schema::hasColumn('stops', 'latitude')) {
                 $table->decimal('latitude', 10, 7)->nullable();
                 $table->decimal('longitude', 10, 7)->nullable();
             }
         });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes_and_stops', function (Blueprint $table) {
            //
        });
    }
};
