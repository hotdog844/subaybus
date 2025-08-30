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
    Schema::create('bus_stops', function (Blueprint $table) {
        $table->id();
        $table->foreignId('route_id')->constrained()->onDelete('cascade');
        $table->string('name');
        $table->decimal('latitude', 10, 7);
        $table->decimal('longitude', 10, 7);
        $table->unsignedInteger('sequence'); // The order of the stop on the route (1, 2, 3...)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_stops');
    }
};
