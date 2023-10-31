<?php

use App\Models\WarehouseItem;
use App\Models\WarehouseItemCategory;
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
        Schema::table('paper_types', function (Blueprint $table) {
            $table->foreignIdFor(WarehouseItemCategory::class)->nullable();
        });

        Schema::table('paper_props', function (Blueprint $table) {
            $table->foreignIdFor(WarehouseItem::class)->nullable();
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
