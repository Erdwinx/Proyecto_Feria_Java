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
        Schema::create('scan_log', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id');
            $table->string('nombre');
            $table->unsignedBigInteger('scanned_at_epoch_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_log');
    }
};
