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
        // Remove the old columns that are causing crashes
        if (Schema::hasColumn('routes', 'start_destination')) $table->dropColumn('start_destination');
        if (Schema::hasColumn('routes', 'end_destination')) $table->dropColumn('end_destination');

        // Ensure new columns exist
        if (!Schema::hasColumn('routes', 'start_location')) $table->string('start_location')->nullable();
        if (!Schema::hasColumn('routes', 'end_location')) $table->string('end_location')->nullable();
        if (!Schema::hasColumn('routes', 'distance')) $table->decimal('distance', 8, 2)->default(0);
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
