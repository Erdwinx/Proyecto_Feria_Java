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
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('package_id')->nullable()->after('customer_id');
            $table->string('tipo_evento')->default('feria')->after('package_id'); // 'concierto', 'feria'
            
            // Agregar foreign key a packages
            $table->foreign('package_id')->references('id')->on('packages')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropColumn(['package_id', 'tipo_evento']);
        });
    }
};
