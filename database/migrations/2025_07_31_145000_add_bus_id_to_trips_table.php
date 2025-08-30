<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('bus_id')->after('user_id')->nullable();

            // Optional: add foreign key if you want
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['bus_id']);
            $table->dropColumn('bus_id');
        });
    }
};

