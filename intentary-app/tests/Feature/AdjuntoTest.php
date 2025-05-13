<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Adjunto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\MovimientoInventario;

class AdjuntoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puede_crearse_un_adjunto()
    {
        $movimiento = MovimientoInventario::factory()->create();

        $adjunto = Adjunto::factory()->create([
            'movimiento_id' => $movimiento->id,
        ]);

        $this->assertDatabaseHas('adjuntos', [
            'id' => $adjunto->id,
            'movimiento_id' => $movimiento->id,
        ]);
    }

    /** @test */
    public function un_adjunto_pertenece_a_un_movimiento()
    {
        $adjunto = Adjunto::factory()->create();

        $this->assertInstanceOf(MovimientoInventario::class, $adjunto->movimiento);
    }
}
