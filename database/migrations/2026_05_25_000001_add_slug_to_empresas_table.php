<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('nombre');
        });

        $empresas = DB::table('empresas')->select('id', 'nombre')->get();
        foreach ($empresas as $empresa) {
            $baseSlug = Str::slug($empresa->nombre ?: 'empresa-'.$empresa->id);
            $slug = $baseSlug;
            $suffix = 1;
            while (DB::table('empresas')->where('slug', $slug)->where('id', '!=', $empresa->id)->exists()) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }
            DB::table('empresas')->where('id', $empresa->id)->update(['slug' => $slug]);
        }

        Schema::table('empresas', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
