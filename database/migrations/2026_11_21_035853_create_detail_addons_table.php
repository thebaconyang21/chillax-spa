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
        Schema::create('detail_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_detail_id')->constrained();
            $table->foreignId('addon_id')->constrained();
            $table->decimal('addon_price', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_addons');
    }
};
