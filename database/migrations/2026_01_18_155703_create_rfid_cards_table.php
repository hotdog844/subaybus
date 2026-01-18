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
    Schema::create('rfid_cards', function (Blueprint $table) {
        $table->id();
        $table->string('uid')->unique(); // The Card ID (e.g., "63 7A 13 39")
        $table->decimal('balance', 8, 2)->default(0.00);
        $table->string('user_type')->default('regular'); // 'regular' or 'student'
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_cards');
    }
};
