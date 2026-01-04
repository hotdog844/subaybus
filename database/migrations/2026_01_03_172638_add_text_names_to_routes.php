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
        // Add the missing text columns if they don't exist
        if (!Schema::hasColumn('routes', 'origin')) {
            $table->string('origin')->nullable()->after('name');
        }
        
        if (!Schema::hasColumn('routes', 'destination')) {
            $table->string('destination')->nullable()->after('origin');
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
