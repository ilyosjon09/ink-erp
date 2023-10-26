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
        Schema::create('oper_days', function (Blueprint $table) {
            $table->id();
            $table->date('operday')->unique();
            $table->boolean('closed')->default(false);
            $table->foreignIdFor(\App\Models\User::class,'created_by');
            $table->foreignIdFor(\App\Models\User::class,'updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oper_days');
    }
};
