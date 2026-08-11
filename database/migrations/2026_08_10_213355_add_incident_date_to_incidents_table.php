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
        Schema::table('incidents', function (Blueprint $table) {
            $table->timestamp('incident_date')->nullable()->after('description');
        });

        // Copiar datos de created_at a incident_date para conservar historial
        \Illuminate\Support\Facades\DB::statement('UPDATE incidents SET incident_date = created_at');
        
        // Hacer la columna obligatoria (PostgreSQL)
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE incidents ALTER COLUMN incident_date SET NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('incident_date');
        });
    }
};
