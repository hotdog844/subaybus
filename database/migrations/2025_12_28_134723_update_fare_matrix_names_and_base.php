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
        // 1. Rename the Vehicle Types to what you requested
    DB::table('fare_matrices')->where('id', 1)->update([
        'vehicle_type' => 'Modern Jeepney (Pueblo)',
        'base_km' => 4 // Default
    ]);
    
    DB::table('fare_matrices')->where('id', 2)->update([
        'vehicle_type' => 'Modern Jeepney (Banica)',
        'base_km' => 4 // Default
    ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
