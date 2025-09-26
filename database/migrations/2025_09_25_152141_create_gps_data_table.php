<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gps_data', function (Blueprint $table) {
            $table->id();
            $table->string('imei')->index();   // tracker device ID
            $table->decimal('lat', 10, 7);     // latitude
            $table->decimal('lng', 10, 7);     // longitude
            $table->timestamps();              // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_data');
    }
};
