<?php

use App\Models\Client;
use App\Models\PaperProp;
use App\Models\ProfitPercentage;
use App\Models\User;
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
            $table->unsignedInteger('code');
            $table->string('item_name');
            $table->foreignIdFor(Client::class);
            $table->foreignIdFor(PaperProp::class);
            $table->integer('amount_per_paper');
            $table->string('printing_type')->index();
            $table->integer('tirage');
            $table->string('item_image');
            $table->integer('additional_tirage');
            $table->foreignIdFor(ProfitPercentage::class);
            $table->unsignedTinyInteger('status')->comment('0 - draft, 1 - in production, 2 - post production, 4 - done, 5 - cancelled');
            $table->foreignIdFor(User::class, 'created_by');
            $table->foreignIdFor(User::class, 'printed_by')->nullable();
            $table->foreignIdFor(User::class, 'processed_by')->nullable();
            $table->timestamp('printed_at');
            $table->timestamp('processesed_at');
            $table->timestamps();

            $table->unique(['code', 'created_at']);
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
