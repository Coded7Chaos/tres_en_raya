<?php

namespace Tests\Feature;

use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_una_partida_que_empieza_con_x(): void
    {
        $this->postJson('/api/games')
            ->assertCreated()
            ->assertJsonPath('next_player', 'X')
            ->assertJsonPath('winner', null);
    }

    public function test_guarda_las_jugadas_y_alterna_turnos(): void
    {
        $game = Game::create();

        $this->postJson("/api/games/{$game->id}/moves", ['x' => 1, 'y' => 1])
            ->assertOk()
            ->assertJsonPath('board.1.1', 'X')
            ->assertJsonPath('next_player', '+');

        $this->assertDatabaseHas('moves', ['game_id' => $game->id, 'player' => 'X', 'x' => 1, 'y' => 1]);
    }

    public function test_rechaza_jugadas_invalidas(): void
    {
        $game = Game::create();
        $this->postJson("/api/games/{$game->id}/moves", ['x' => 0, 'y' => 0]);

        $this->postJson("/api/games/{$game->id}/moves", ['x' => 3, 'y' => 0])->assertStatus(422);
        $this->postJson("/api/games/{$game->id}/moves", ['x' => 0, 'y' => -1])->assertStatus(422);
        $this->postJson("/api/games/{$game->id}/moves", ['x' => 0, 'y' => 0])
            ->assertStatus(422)
            ->assertJsonPath('message', 'La casilla (0, 0) ya está ocupada.');

        $this->assertSame(1, $game->moves()->count());
    }

    public function test_registra_al_ganador_y_las_estadisticas(): void
    {
        $game = Game::create();

        foreach ([[0, 0], [0, 1], [1, 0], [1, 1], [2, 0]] as [$x, $y]) {
            $response = $this->postJson("/api/games/{$game->id}/moves", compact('x', 'y'));
        }

        $response->assertJsonPath('winner', 'X')->assertJsonPath('is_over', true);
        $this->assertDatabaseHas('games', ['id' => $game->id, 'status' => 'won', 'winner' => 'X']);

        $this->getJson('/api/stats')->assertJsonPath('x_wins', 1)->assertJsonPath('plus_wins', 0);
    }
}
