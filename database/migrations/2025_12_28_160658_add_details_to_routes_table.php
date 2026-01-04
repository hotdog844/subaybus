<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            // Check if columns exist before adding them to prevent errors
            if (!Schema::hasColumn('routes', 'start_location')) {
                $table->string('start_location')->nullable()->after('name');
            }
            if (!Schema::hasColumn('routes', 'end_location')) {
                $table->string('end_location')->nullable()->after('start_location');
            }
            if (!Schema::hasColumn('routes', 'distance')) {
                $table->decimal('distance', 8, 2)->default(0)->after('end_location');
            }
        });
    }

    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn(['start_location', 'end_location', 'distance']);
        });
    }
};