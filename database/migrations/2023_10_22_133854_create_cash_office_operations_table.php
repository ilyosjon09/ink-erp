<?php

use App\Models\CashOfficeAccount;
use App\Models\CashOfficeOperationTemplate;
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
        Schema::create('cash_office_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(CashOfficeAccount::class);
            $table->foreignIdFor(CashOfficeOperationTemplate::class)->nullable();
            $table->string('purpose')->nullable();
            $table->unsignedTinyInteger('operation')->comment('0 - add, 1 - subtract');
            $table->bigInteger('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_office_operations');
    }
};
