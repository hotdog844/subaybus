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
        // Add the missing capacity column, default to 40 seats
        $table->integer('capacity')->default(40)->after('status'); 
    });
}

public function down()
{
    Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn('capacity');
    });
}
};
