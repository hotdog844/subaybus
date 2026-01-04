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
        // Add the IMEI column (nullable, because not all buses might have trackers yet)
        $table->string('imei')->nullable()->unique()->after('bus_number');
    });
}

public function down()
{
    Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn('imei');
    });
}
};
