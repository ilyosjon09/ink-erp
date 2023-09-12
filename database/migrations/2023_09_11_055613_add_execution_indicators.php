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
        Schema::table('order_service_price', function (Blueprint $table) {
            $table->addColumn('boolean', 'completed')->default(false)->after('price');
        });
        Schema::table('order_printing_form', function (Blueprint $table) {
            $table->addColumn('boolean', 'completed')->default(false)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_service_price', function (Blueprint $table) {
            $table->dropColumn('completed');
        });
        Schema::table('order_printing_form', function (Blueprint $table) {
            $table->dropColumn('completed');
        });
    }
};
