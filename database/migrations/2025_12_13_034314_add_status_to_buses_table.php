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
    Schema::table('buses', function (Blueprint $table) {
        // Only add passenger_count if it's missing
        if (!Schema::hasColumn('buses', 'passenger_count')) {
            $table->integer('passenger_count')->default(0)->after('bus_number');
        }

        // Only add status if it's missing
        if (!Schema::hasColumn('buses', 'status')) {
            $table->string('status')->default('active')->after('passenger_count');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            //
        });
    }
};
