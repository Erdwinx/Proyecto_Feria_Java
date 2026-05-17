<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('purchase_queue_requests')) {
            return;
        }

        Schema::create('purchase_queue_requests', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('customer_id');
            $table->json('payload');
            $table->string('status')->default('queued');
            $table->json('result')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_queue_requests');
    }
};
