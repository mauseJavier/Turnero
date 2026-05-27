<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->string('origen')->default('admin')->after('estado');
            $table->string('token_publico_reserva', 64)->nullable()->unique()->after('origen');
            $table->dateTime('fecha_vencimiento_pago')->nullable()->after('token_publico_reserva');
            $table->string('pago_proveedor')->nullable()->after('precio_final');
            $table->string('pago_preference_id')->nullable()->after('pago_proveedor');
            $table->string('pago_id')->nullable()->after('pago_preference_id');
            $table->string('pago_status')->nullable()->after('pago_id');
            $table->string('pago_status_detail')->nullable()->after('pago_status');
            $table->string('pago_init_point')->nullable()->after('pago_status_detail');
            $table->dateTime('pago_expires_at')->nullable()->after('pago_init_point');
            $table->dateTime('pagado_at')->nullable()->after('pago_expires_at');
            $table->decimal('monto_pagado', 10, 2)->nullable()->after('pagado_at');
            $table->string('moneda', 8)->nullable()->after('monto_pagado');
        });

        DB::table('turnos')->where('estado', 'pendiente')->update(['estado' => 'pendiente_pago']);
    }

    public function down(): void
    {
        Schema::table('turnos', function (Blueprint $table) {
            $table->dropColumn([
                'origen',
                'token_publico_reserva',
                'fecha_vencimiento_pago',
                'pago_proveedor',
                'pago_preference_id',
                'pago_id',
                'pago_status',
                'pago_status_detail',
                'pago_init_point',
                'pago_expires_at',
                'pagado_at',
                'monto_pagado',
                'moneda',
            ]);
        });
    }
};
