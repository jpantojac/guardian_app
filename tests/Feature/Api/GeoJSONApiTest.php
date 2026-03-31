<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * GeoJSONApiTest — Pruebas de integración para los endpoints GeoJSON.
 *
 * Entregable E3 — GuardianApp MVP v1.0
 * Autor: Julián Pantoja Clavijo — UNAD 2026
 *
 * Endpoints cubiertos:
 *   GET /api/geojson              (FeatureCollection de incidentes)
 *   GET /api/localidades-geojson  (FeatureCollection de localidades)
 *
 * Estándar: GeoJSON RFC 7946
 */
class GeoJSONApiTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    //  GET /api/geojson
    // ─────────────────────────────────────────────

    /** @test */
    public function endpoint_geojson_es_publico(): void
    {
        $response = $this->getJson('/api/geojson');

        // No debe requerir autenticación
        $response->assertStatus(200);
    }

    /** @test */
    public function respuesta_geojson_tiene_estructura_feature_collection(): void
    {
        $response = $this->getJson('/api/geojson');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'type',
                     'features',
                 ])
                 ->assertJsonPath('type', 'FeatureCollection');
    }

    /** @test */
    public function features_geojson_tienen_estructura_correcta_rfc_7946(): void
    {
        // Crear un incidente con geometría (requiere PostGIS)
        // Si no hay PostGIS, el test valida la estructura vacía
        $response = $this->getJson('/api/geojson');

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertEquals('FeatureCollection', $data['type']);
        $this->assertIsArray($data['features']);

        // Si hay features, verificar estructura de cada una
        foreach ($data['features'] as $feature) {
            $this->assertEquals('Feature', $feature['type']);
            $this->assertArrayHasKey('geometry', $feature);
            $this->assertArrayHasKey('properties', $feature);

            // La geometría debe ser Point (RFC 7946)
            if ($feature['geometry'] !== null) {
                $this->assertEquals('Point', $feature['geometry']['type']);
                $this->assertCount(2, $feature['geometry']['coordinates']); // [lng, lat]
            }

            // Properties requeridas
            $this->assertArrayHasKey('id', $feature['properties']);
            $this->assertArrayHasKey('category', $feature['properties']);
            $this->assertArrayHasKey('status', $feature['properties']);
            $this->assertArrayHasKey('privacy_level', $feature['properties']);
            $this->assertArrayHasKey('created_at', $feature['properties']);
        }
    }

    /** @test */
    public function geojson_acepta_filtro_por_days(): void
    {
        $response = $this->getJson('/api/geojson?days=7');

        $response->assertStatus(200)
                 ->assertJsonPath('type', 'FeatureCollection');
    }

    /** @test */
    public function geojson_acepta_filtro_por_start_date_y_end_date(): void
    {
        $response = $this->getJson('/api/geojson?start_date=2026-01-01&end_date=2026-03-31');

        $response->assertStatus(200)
                 ->assertJsonPath('type', 'FeatureCollection');
    }

    /** @test */
    public function geojson_acepta_filtro_por_year_y_month(): void
    {
        $response = $this->getJson('/api/geojson?year=2026&month=3');

        $response->assertStatus(200)
                 ->assertJsonPath('type', 'FeatureCollection');
    }

    /** @test */
    public function geojson_acepta_filtro_por_category_id(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson('/api/geojson?category_id=' . $category->id);

        $response->assertStatus(200)
                 ->assertJsonPath('type', 'FeatureCollection');
    }

    /** @test */
    public function geojson_acepta_filtro_por_multiples_categorias(): void
    {
        $cat1 = Category::factory()->create();
        $cat2 = Category::factory()->create();

        $response = $this->getJson(
            '/api/geojson?categories[]=' . $cat1->id . '&categories[]=' . $cat2->id
        );

        $response->assertStatus(200)
                 ->assertJsonPath('type', 'FeatureCollection');
    }

    /** @test */
    public function geojson_incidente_anonimo_reporta_nombre_anonimo(): void
    {
        $user     = User::factory()->create(['name' => 'María García']);
        $category = Category::factory()->create();

        $incident = Incident::factory()->create([
            'user_id'       => $user->id,
            'category_id'   => $category->id,
            'privacy_level' => 'ANONYMOUS',
        ]);

        // Verificar que el accessor retorna Anónimo
        $this->assertEquals('Anónimo', $incident->reporter_name);
        $this->assertNotEquals('María García', $incident->reporter_name);
    }

    /** @test */
    public function geojson_incidente_identificado_muestra_nombre(): void
    {
        $user     = User::factory()->create(['name' => 'Pedro Suárez']);
        $category = Category::factory()->create();

        $incident = Incident::factory()->create([
            'user_id'       => $user->id,
            'category_id'   => $category->id,
            'privacy_level' => 'IDENTIFIED',
        ]);

        $this->assertEquals('Pedro Suárez', $incident->reporter_name);
    }

    /** @test */
    public function respuesta_geojson_content_type_es_json(): void
    {
        $response = $this->getJson('/api/geojson');

        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'application/json');
    }

    // ─────────────────────────────────────────────
    //  GET /api/localidades-geojson
    // ─────────────────────────────────────────────

    /** @test */
    public function endpoint_localidades_geojson_es_publico(): void
    {
        $response = $this->getJson('/api/localidades-geojson');

        // No debe requerir autenticación
        $response->assertStatus(200);
    }

    /** @test */
    public function localidades_geojson_tiene_estructura_feature_collection(): void
    {
        $response = $this->getJson('/api/localidades-geojson');

        $response->assertStatus(200)
                 ->assertJsonPath('type', 'FeatureCollection')
                 ->assertJsonStructure([
                     'type',
                     'features',
                 ]);
    }

    /** @test */
    public function localidades_features_tienen_tipo_polygon_o_multipolygon(): void
    {
        $response = $this->getJson('/api/localidades-geojson');
        $data     = $response->json();

        $this->assertEquals('FeatureCollection', $data['type']);

        foreach ($data['features'] as $feature) {
            $this->assertEquals('Feature', $feature['type']);
            $this->assertArrayHasKey('nombre', $feature['properties']);
            $this->assertArrayHasKey('id', $feature['properties']);

            if ($feature['geometry'] !== null) {
                $this->assertContains(
                    $feature['geometry']['type'],
                    ['Polygon', 'MultiPolygon']
                );
            }
        }
    }

    /** @test */
    public function localidades_geojson_retorna_array_de_features(): void
    {
        $response = $this->getJson('/api/localidades-geojson');

        $response->assertStatus(200);
        $features = $response->json('features');
        $this->assertIsArray($features);
    }
}
