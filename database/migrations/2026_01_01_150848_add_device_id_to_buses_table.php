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
        // Add device_id column, allow it to be null for old buses
        $table->string('device_id')->nullable()->after('bus_number');
    });
}

public function down()
{
    Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn('device_id');
    });
}
};
