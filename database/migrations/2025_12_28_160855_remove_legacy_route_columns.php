<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            // Drop the old columns if they exist so they don't block inserts
            if (Schema::hasColumn('routes', 'start_destination')) {
                $table->dropColumn('start_destination');
            }
            if (Schema::hasColumn('routes', 'end_destination')) {
                $table->dropColumn('end_destination');
            }
        });
    }

    public function down()
    {
        // Optional: Add them back if we ever rolled back (unlikely needed)
        Schema::table('routes', function (Blueprint $table) {
            $table->string('start_destination')->nullable();
            $table->string('end_destination')->nullable();
        });
    }
};