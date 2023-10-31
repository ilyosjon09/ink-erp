<?php

use App\Models\WarehouseItemBatch;
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
        Schema::table('warehouse_operations', function (Blueprint $table) {
            $table->dropColumn('item_id');
            $table->foreignIdFor(WarehouseItemBatch::class);
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_operations', function (Blueprint $table) {
            //
        });
    }
};
