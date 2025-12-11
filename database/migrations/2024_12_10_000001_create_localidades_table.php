<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('localidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->nullable(); // Codigo de la localidad (ej: '01', '02')
            // Multipolygon for the locality boundaries
            $table->geometry('geom', subtype: 'multipolygon', srid: 4326);
            $table->timestamps();
        });

        // Add spatial index
        DB::statement('CREATE INDEX localidades_geom_idx ON localidades USING GIST (geom);');
    }

    public function down(): void
    {
        Schema::dropIfExists('localidades');
    }
};
