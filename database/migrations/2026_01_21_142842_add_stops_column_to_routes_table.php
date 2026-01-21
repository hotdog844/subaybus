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
    Schema::table('routes', function (Blueprint $table) {
        // I-check muna kung WALA pa ang column bago mag-add
        if (!Schema::hasColumn('routes', 'stops')) {
            $table->longText('stops')->nullable()->after('path_data');
        }
    });
}

public function down()
{
    Schema::table('routes', function (Blueprint $table) {
        $table->dropColumn('stops');
    });
}
};
