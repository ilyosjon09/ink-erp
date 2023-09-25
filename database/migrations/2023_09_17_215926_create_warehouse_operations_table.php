<?php

use App\Models\User;
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
        Schema::create('warehouse_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WarehouseItem::class, 'item_id');
            $table->tinyInteger('operation')->comment('0 - subtract, 1 - add');
            $table->unsignedInteger('amount');
            $table->unsignedBigInteger('price');
            $table->string('comment')->nullable();
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_operations');
    }
};
