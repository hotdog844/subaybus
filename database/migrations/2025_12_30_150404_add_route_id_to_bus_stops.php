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
    Schema::table('bus_stops', function (Blueprint $table) {
        // 1. Check if it exists first to prevent errors
        if (!Schema::hasColumn('bus_stops', 'route_id')) {
            // 2. Add the missing column (Link to routes table)
            $table->foreignId('route_id')
                  ->nullable() // Make it nullable first to prevent conflicts with existing rows
                  ->constrained('routes')
                  ->onDelete('cascade'); // If route is deleted, delete its stops too
        }
        
        // 3. Ensure 'sequence' exists too (Used for ordering)
        if (!Schema::hasColumn('bus_stops', 'sequence')) {
            $table->integer('sequence')->default(1);
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bus_stops', function (Blueprint $table) {
            //
        });
    }
};
