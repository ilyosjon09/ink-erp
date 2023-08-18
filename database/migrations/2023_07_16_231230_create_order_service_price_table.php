<?php

use App\Models\Order;
use App\Models\ServicePrice;
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
        Schema::create('order_service_price', function (Blueprint $table) {
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(ServicePrice::class);
            $table->integer('price');
            $table->timestamps();

            $table->index('order_id');
            $table->index('service_price_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_service');
    }
};
