<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\OperDay::class);
        });

        Schema::table('warehouse_operations', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\OperDay::class);
        });

        Schema::table('cash_office_operations', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\OperDay::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_essential_tables', function (Blueprint $table) {
            //
        });
    }
};
