<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            
            // Connects to 'routes' table
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            
            // Connects to 'stops' table
            $table->foreignId('stop_id')->constrained('stops')->onDelete('cascade');
            
            // Defines the order (1st stop, 2nd stop, etc.)
            $table->integer('order'); 
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('route_stops');
    }
};