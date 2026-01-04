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
        // Add columns for Latitude and Longitude
        // 10 digits total, 7 after the decimal point (high precision GPS)
        $table->decimal('lat', 10, 7)->nullable()->after('status');
        $table->decimal('lng', 10, 7)->nullable()->after('lat');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn(['lat', 'lng']);
    });
}
};
