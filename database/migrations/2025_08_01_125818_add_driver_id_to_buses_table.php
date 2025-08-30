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
            // Add the new driver_id column. It's nullable in case a bus has no assigned driver.
            $table->foreignId('driver_id')->nullable()->after('id')->constrained()->onDelete('set null');
            
            // Drop the old text-based column
            $table->dropColumn('driver_name');
        });
    }

    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            // Re-add the old column if we roll back
            $table->string('driver_name');

            // Drop the foreign key and the column
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
        });
    }
};
