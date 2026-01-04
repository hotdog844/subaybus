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
    // 1. Create table if it doesn't exist
    if (!Schema::hasTable('feedback')) {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bus_id')->nullable()->constrained('buses')->onDelete('set null');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->string('status')->default('new'); // new, read, archived
            $table->timestamps();
        });
    } 
    // 2. Repair table if it exists but is missing columns
    else {
        Schema::table('feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback', 'bus_id')) {
                $table->foreignId('bus_id')->nullable()->constrained('buses')->onDelete('set null')->after('user_id');
            }
            if (!Schema::hasColumn('feedback', 'rating')) {
                $table->integer('rating')->after('bus_id');
            }
            if (!Schema::hasColumn('feedback', 'status')) {
                $table->string('status')->default('new')->after('comment');
            }
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            //
        });
    }
};
