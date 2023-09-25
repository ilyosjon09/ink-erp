<?php

use App\Models\PaperProp;
use App\Models\User;
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
        Schema::create('warehouse_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WarehouseItemCategory::class, 'category_id')->nullable();
            $table->unsignedInteger('code')->unique();
            $table->string('name');
            $table->string('measurement_unit');
            $table->unsignedInteger('grammage')->nullable();
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
        Schema::dropIfExists('warehouse_items');
    }
};
