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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('therapist_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->decimal('reserved_price', 10, 2);
            $table->enum('appointment_status', ['pending', 'confirmed', 'arrived', 'in_service', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
