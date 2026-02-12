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
        Schema::table('pedidos', function (Blueprint $table) {
            // Agregar columnas de pago si no existen
            if (!Schema::hasColumn('pedidos', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid'])->default('pending')->after('status');
            }
            
            if (!Schema::hasColumn('pedidos', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'card', 'transfer'])->nullable()->after('payment_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
            
            if (Schema::hasColumn('pedidos', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
        });
    }
};
