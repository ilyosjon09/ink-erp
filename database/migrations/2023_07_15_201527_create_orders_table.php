<?php

use App\Models\Client;
use App\Models\PaperProp;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(PaperProp::class);
            $table->integer('amount_per_paper');
            $table->string('printing_method')->index();
            $table->integer('tirage');
            $table->integer('additional_triage');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
