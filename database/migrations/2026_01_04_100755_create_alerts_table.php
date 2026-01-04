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
    Schema::create('alerts', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // e.g., "Heavy Traffic"
        $table->text('message'); // e.g., "Avoid Roxas Ave..."
        $table->string('type')->default('info'); // info, warning, danger
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
