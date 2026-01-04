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
    Schema::create('fare_matrices', function (Blueprint $table) {
        $table->id();
        $table->string('vehicle_type'); // e.g. "Modern Jeepney", "Traditional"
        $table->decimal('base_fare', 8, 2);      // Price for first 4km
        $table->decimal('per_km_rate', 8, 2);    // Price added per km after 4km
        $table->decimal('discount_base', 8, 2);  // Student/Senior Base
        $table->decimal('discount_per_km', 8, 2);// Student/Senior Per Km
        $table->integer('base_km')->default(4);  // Standard is 4km
        $table->timestamps();
    });

    // Seed default LTFRB Data
    DB::table('fare_matrices')->insert([
        [
            'vehicle_type' => 'Modern Jeepney (Aircon)',
            'base_fare' => 15.00,
            'per_km_rate' => 2.20,
            'discount_base' => 12.00,
            'discount_per_km' => 1.80,
            'base_km' => 4,
            'created_at' => now(), 'updated_at' => now()
        ],
        [
            'vehicle_type' => 'Traditional Jeepney',
            'base_fare' => 13.00,
            'per_km_rate' => 1.80,
            'discount_base' => 10.40,
            'discount_per_km' => 1.50,
            'base_km' => 4,
            'created_at' => now(), 'updated_at' => now()
        ]
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fare_matrices');
    }
};
