<?php

use App\Models\PaperProp;
use App\Models\PaperType;
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
        Schema::create('warehouse_item_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('for_paper')->default(false);
            $table->foreignIdFor(PaperType::class)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_item_categories');
    }
};
