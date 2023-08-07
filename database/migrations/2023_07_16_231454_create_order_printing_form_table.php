<?php

use App\Models\Order;
use App\Models\PrintingForm;
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
        Schema::create('order_printing_form', function (Blueprint $table) {
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(PrintingForm::class);
            $table->integer('price');
            $table->boolean('is_double_four')->default(false);
            $table->timestamps();

            $table->index('order_id');
            $table->index('printing_form_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_printing_form');
    }
};
