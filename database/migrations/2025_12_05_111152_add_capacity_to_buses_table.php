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
        $table->integer('current_load')->default(0); // Current passengers
        $table->integer('max_capacity')->default(40); // Max seats
    });
}

public function down(): void
{
    Schema::table('buses', function (Blueprint $table) {
        $table->dropColumn(['current_load', 'max_capacity']);
    });
}
};
