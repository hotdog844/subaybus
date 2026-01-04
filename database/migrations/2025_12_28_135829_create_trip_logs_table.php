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
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->string('driver_name');
            $table->string('bus_number');
            $table->string('route_name');
            $table->integer('passenger_count');
            $table->decimal('total_revenue', 10, 2);
            // FIX: Make these nullable to satisfy MySQL strict mode
            $table->timestamp('shift_start')->nullable();
            $table->timestamp('shift_end')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};
