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
        Schema::table('warehouse_item_categories', function (Blueprint $table) {
            $table->dropColumn(['bindable_id', 'bindable_type']);
            $table->boolean('for_paper')->default(false);
        });

        Schema::table('warehouse_items', function (Blueprint $table) {
            $table->dropColumn(['association_id', 'association_type']);
            $table->boolean('for_paper')->default(false);
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
