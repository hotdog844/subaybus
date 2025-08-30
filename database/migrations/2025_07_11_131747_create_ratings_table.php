<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id');
            $table->tinyInteger('rating'); // Rating from 1 to 5
            $table->timestamps();

            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ratings');
    }
};
