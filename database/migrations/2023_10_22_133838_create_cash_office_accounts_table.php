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
        Schema::create('cash_office_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('type')->comment('0 - cash, 1 - card, 2 - bank account, 3 - client account');
            $table->string('number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_office_accounts');
    }
};
