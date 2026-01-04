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
    Schema::create('stops', function (Blueprint $table) {
        $table->id();
        $table->string('route_name'); // e.g., "PdP Red"
        $table->string('name');       // e.g., "Roxas City Hall"
        $table->decimal('lat', 10, 7);
        $table->decimal('lng', 10, 7);
        $table->integer('order_index'); // To show them in order (1st, 2nd, 3rd)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stops');
    }
};
