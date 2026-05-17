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
        Schema::create('packages', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nombre');
            $table->string('tipo_evento'); // 'concierto', 'feria', etc.
            $table->date('fecha_evento');
            $table->longText('qr_text')->nullable();
            $table->timestamp('qr_generated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
