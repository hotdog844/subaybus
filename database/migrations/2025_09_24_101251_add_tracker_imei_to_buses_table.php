<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
            Schema::table('buses', function (Blueprint $table) {
        // Add the new column after 'plate_number' for organization
        $table->string('tracker_imei', 20)->nullable()->unique()->after('plate_number');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn('tracker_imei');
    });
    }
};
