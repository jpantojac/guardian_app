<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('upz_code')->unique();
            $table->string('locality_name');
            // MultiPolygon to handle complex boundaries
            $table->geometry('boundary', subtype: 'multipolygon', srid: 4326);
            $table->timestamps();
        });

        DB::statement('CREATE INDEX zones_boundary_idx ON zones USING GIST (boundary);');
    }

    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
