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
        // We use 'start_location' and 'end_location' because that is what your DB has
        if (!Schema::hasColumn('routes', 'origin_lat')) {
            $table->decimal('origin_lat', 10, 8)->nullable()->after('start_location');
        }
        if (!Schema::hasColumn('routes', 'origin_lng')) {
            $table->decimal('origin_lng', 11, 8)->nullable()->after('origin_lat');
        }
        if (!Schema::hasColumn('routes', 'dest_lat')) {
            $table->decimal('dest_lat', 10, 8)->nullable()->after('end_location');
        }
        if (!Schema::hasColumn('routes', 'dest_lng')) {
            $table->decimal('dest_lng', 11, 8)->nullable()->after('dest_lat');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            //
        });
    }
};
