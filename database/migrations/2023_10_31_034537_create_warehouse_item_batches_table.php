<?php

use App\Models\WarehouseItem;
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
        Schema::create('warehouse_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WarehouseItem::class);
            $table->bigInteger('in_quantity');
            $table->bigInteger('in_price');
            $table->bigInteger('out_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_item_batches');
    }
};
