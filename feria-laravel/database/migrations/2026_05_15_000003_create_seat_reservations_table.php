<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seat_reservations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_evento');
            $table->string('category')->nullable();
            $table->string('seat_number')->nullable();
            $table->string('ticket_id')->nullable();
            $table->string('status')->default('reserved');
            $table->timestamps();

            $table->unique(['fecha_evento', 'category', 'seat_number'], 'seat_res_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seat_reservations');
    }
};
