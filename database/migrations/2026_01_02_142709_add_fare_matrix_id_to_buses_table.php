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
    Schema::table('buses', function (Blueprint $table) {
        // This creates a column to store the ID of the fare matrix
        $table->unsignedBigInteger('fare_matrix_id')->nullable()->after('route_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            //
        });
    }
};
