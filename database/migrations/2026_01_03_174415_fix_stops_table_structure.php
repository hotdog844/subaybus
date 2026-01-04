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
    Schema::table('stops', function (Blueprint $table) {
        
        // 1. Add route_id (Foreign Key)
        if (!Schema::hasColumn('stops', 'route_id')) {
            $table->unsignedBigInteger('route_id')->after('id')->nullable();
            // Optional: Add foreign key constraint if you want strict linking
            // $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
        }

        // 2. Add name
        if (!Schema::hasColumn('stops', 'name')) {
            $table->string('name')->after('route_id')->nullable();
        }

        // 3. Add coordinates
        if (!Schema::hasColumn('stops', 'latitude')) {
            $table->decimal('latitude', 10, 8)->nullable()->after('name');
        }
        if (!Schema::hasColumn('stops', 'longitude')) {
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        }

        // 4. Add sequence (The error mentioned "order by sequence")
        if (!Schema::hasColumn('stops', 'sequence')) {
            $table->integer('sequence')->default(0)->after('longitude');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
