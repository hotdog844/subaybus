<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Update USERS table to hold the Wallet Balance
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('email');
            $table->boolean('is_id_verified')->default(false)->after('wallet_balance'); // For Phase 3
        });

        // 2. Create CARDS table (The RFID Cards)
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_uid')->unique(); // The code inside the RFID Card
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active, blocked, lost
            $table->timestamps();
        });

        // 3. Create TRANSACTIONS table (History)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('card_id')->nullable()->constrained(); 
            $table->string('type'); // 'payment', 'load', 'refund'
            $table->decimal('amount', 10, 2);
            $table->string('reference_id')->nullable(); // For tracking
            $table->string('description')->nullable(); // e.g. "Fare for Bus 101"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('cards');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_balance', 'is_id_verified']);
        });
    }
};