<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Adjunto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdjuntoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_subir_adjunto()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('documento.pdf', 500, 'application/pdf');

        $response = $this->postJson('/api/adjuntos', [
            'tipo' => 'App\\Models\\Producto',
            'relacionado_id' => 1,
            'archivo' => $file,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('adjuntos', [
            'nombre_original' => 'documento.pdf',
            'extension' => 'pdf',
        ]);

        Storage::disk('public')->assertExists('adjuntos/' . $response->json('nombre_guardado'));
    }
}
