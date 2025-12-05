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
    Schema::table('routes', function (Blueprint $table) {
        // Stores the list of [lat, lng] as a JSON string
        $table->longText('path_data')->nullable(); 
        // Stores the line color (e.g., "#00b894")
        $table->string('color')->default('#00b894'); 
    });
}

public function down(): void
{
    Schema::table('routes', function (Blueprint $table) {
        $table->dropColumn(['path_data', 'color']);
    });
}
};
