<?php

use App\Models\Service;
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
        Schema::create('service_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Service::class);
            $table->char('print_type', '4');
            $table->unsignedTinyInteger('calc_method')->default(0)->comment('0 - per triage, 1 - per item');
            $table->unsignedBigInteger('price_before_1k');
            $table->unsignedBigInteger('price_after_1k')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_prices');
    }
};
