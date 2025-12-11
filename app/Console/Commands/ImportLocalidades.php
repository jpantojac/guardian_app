<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Schema;

class ImportLocalidades extends Command
{
    protected $signature = 'localidades:import';
    protected $description = 'Import Bogotá localidades shapefile into PostGIS';

    public function handle()
    {
        $this->info('Starting Localidades import...');

        $shpPath = base_path('database/geodata/localidades_Bogota/Loca.shp');

        if (!file_exists($shpPath)) {
            $this->error("Shapefile not found at: $shpPath");
            return 1;
        }

        // Explicit path to shp2pgsql provided by user
        $shp2pgsql = '"C:\Program Files\PostgreSQL\15\bin\shp2pgsql.exe"';

        // Check if shp2pgsql is available
        $check = Process::run("$shp2pgsql -?");
        if ($check->failed()) {
            $this->error('shp2pgsql tool not found. Please ensure PostGIS is installed and shp2pgsql is in your PATH.');
            // Fallback or warning? For now error out as it's critical.
            return 1;
        }

        $this->info('Converting shapefile to SQL...');

        // -s 4326: Transform to WGS84
        // -d: Drop table and recreate (we might want -a to append if table exists and migrations created it, but migrations created it empty)
        // Actually, our migration created the table structure. 
        // shp2pgsql -a appends. 
        // We need to map columns correctly. shp2pgsql usually creates table based on DBF columns.
        // Our migration created 'localidades' with 'nombre', 'codigo', 'geom'.
        // The shapefile likely has different column names.

        // Let's inspect the shapefile columns content first if we could, but we can't easily.
        // Alternative: Use shp2pgsql to dump to a temporary table, then insert into ours.

        $tempTable = 'temp_localidades_import';

        // Generate SQL for temp table
        // -I: Create spatial index
        // -s 4326: SRID
        // -W "UTF-8": Encoding
        $process = Process::run("$shp2pgsql -d -s 4326 -I -W \"ISO-8859-1\" \"$shpPath\" $tempTable");

        if ($process->failed()) {
            $this->error('Failed to generate SQL from shapefile.');
            $this->error($process->errorOutput());
            return 1;
        }

        $sql = $process->output();

        $this->info('Executing SQL import...');

        try {
            DB::unprepared($sql);

            $this->info("Imported into $tempTable. Mapping to 'localidades' table...");

            // Map columns. We need to know what the shapefile columns are.
            // Usually DBF has 'LocNombre' or similar. 
            // Let's assume standard Bogota IDenc names: 'LocNombre', 'LocCodigo'.
            // If we are unsure, we could fetch one row from temp table and check keys.

            $example = DB::table($tempTable)->first();
            $columns = array_keys((array) $example);
            $this->info("Columns found: " . implode(', ', $columns));

            // Try to find matching columns
            $colName = collect($columns)->first(fn($c) => stripos($c, 'nombre') !== false || stripos($c, 'name') !== false) ?? 'LocNombre';
            $colCode = collect($columns)->first(fn($c) => stripos($c, 'codigo') !== false || stripos($c, 'code') !== false) ?? 'LocCodigo';
            $colGeom = 'geom'; // shp2pgsql default

            $this->info("Mapping: $colName -> nombre, $colCode -> codigo");

            DB::statement("TRUNCATE TABLE localidades RESTART IDENTITY CASCADE");

            DB::statement("
                INSERT INTO localidades (nombre, codigo, geom, created_at, updated_at)
                SELECT \"$colName\", \"$colCode\", geom, NOW(), NOW()
                FROM $tempTable
            ");

            // Clean up
            Schema::dropIfExists($tempTable);

            $count = DB::table('localidades')->count();
            $this->info("Successfully imported $count localidades.");

        } catch (\Exception $e) {
            $this->error("Database error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
