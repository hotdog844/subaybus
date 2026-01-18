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
    Schema::create('transaction_logs', function (Blueprint $table) {
        $table->id();
        $table->string('rfid_uid');
        $table->string('transaction_type'); // 'PAYMENT' or 'LOAD'
        $table->decimal('amount', 8, 2);
        $table->string('location')->nullable(); // Bus Stop Name or GPS
        $table->timestamps(); // Records the EXACT time of tap
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};
