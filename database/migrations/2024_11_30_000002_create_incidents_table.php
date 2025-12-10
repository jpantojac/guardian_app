<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->text('description')->nullable();
            // We use a raw SQL statement for the geometry column to ensure PostGIS compatibility
            // if the laravel helper isn't fully configured or package is missing.
            // However, Laravel 11 supports geometry types natively for Postgres.
            $table->geometry('location', subtype: 'point', srid: 4326);
            $table->string('status')->default('reported'); // reported, verified, rejected
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        // Add spatial index
        DB::statement('CREATE INDEX incidents_location_idx ON incidents USING GIST (location);');
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
