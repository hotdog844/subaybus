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
        // Check if table exists to prevent errors during refresh
        if (!Schema::hasTable('feedback')) {
            Schema::create('feedback', function (Blueprint $table) {
                $table->id();
                
                // 1. Link to the User (Optional, in case they delete their account)
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                
                // 2. Link to the Bus (Crucial for your report!)
                // This connects to your existing 'buses' table created in 2025_07_10...
                $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade');

                // 3. The Feedback Data
                $table->integer('rating'); // 1 to 5 stars
                $table->text('comment')->nullable(); // The actual message
                
                // 4. Admin Status (New, Read, Replied)
                $table->string('status')->default('new');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};