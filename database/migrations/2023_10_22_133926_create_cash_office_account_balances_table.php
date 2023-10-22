<?php

use App\Models\CashOfficeAccount;
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
        Schema::create('cash_office_account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CashOfficeAccount::class);
            $table->date('operday')->index();
            $table->bigInteger('balance');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_office_account_balances');
    }
};
