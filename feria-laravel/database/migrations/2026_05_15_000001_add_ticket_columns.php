<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (!Schema::hasColumn('tickets', 'tipo_evento')) {
                $table->string('tipo_evento')->nullable()->after('fecha_evento');
            }

            if (!Schema::hasColumn('tickets', 'category')) {
                $table->string('category')->nullable()->after('tipo_evento');
            }

            if (!Schema::hasColumn('tickets', 'package_id')) {
                $table->string('package_id')->nullable()->after('category');
            }

            if (!Schema::hasColumn('tickets', 'seat_numbers')) {
                $table->json('seat_numbers')->nullable()->after('package_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'seat_numbers')) {
                $table->dropColumn('seat_numbers');
            }
            if (Schema::hasColumn('tickets', 'package_id')) {
                $table->dropColumn('package_id');
            }
            if (Schema::hasColumn('tickets', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('tickets', 'tipo_evento')) {
                $table->dropColumn('tipo_evento');
            }
        });
    }
};
