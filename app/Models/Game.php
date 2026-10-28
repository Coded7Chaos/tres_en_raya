<?php

namespace App\Models;

use App\Game\TicTacToe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Game extends Model
{
    protected $fillable = ['status', 'winner'];

    protected $attributes = [
        'status' => 'playing',
    ];

    public function moves(): HasMany
    {
        return $this->hasMany(Move::class)->orderBy('id');
    }

    /**
     * Reconstruye el motor del juego reproduciendo las jugadas guardadas.
     */
    public function engine(): TicTacToe
    {
        return TicTacToe::fromMoves($this->moves);
    }

    /**
     * Valida la jugada con el motor y, si es válida, la guarda.
     *
     * @throws \App\Game\InvalidMoveException
     */
    public function play(int $x, int $y): TicTacToe
    {
        $engine = $this->engine();
        $player = $engine->play($x, $y);

        DB::transaction(function () use ($engine, $player, $x, $y) {
            $this->moves()->create(compact('player', 'x', 'y'));

            $this->update([
                'status' => $engine->winner() ? 'won' : ($engine->isDraw() ? 'draw' : 'playing'),
                'winner' => $engine->winner(),
            ]);
        });

        $this->unsetRelation('moves');

        return $engine;
    }

    public function toState(?TicTacToe $engine = null): array
    {
        $engine ??= $this->engine();

        return [
            'id' => $this->id,
            'board' => $engine->board(),
            'next_player' => $engine->isOver() ? null : $engine->nextPlayer(),
            'winner' => $engine->winner(),
            'winning_line' => $engine->winningLine(),
            'is_draw' => $engine->isDraw(),
            'is_over' => $engine->isOver(),
            'moves' => $this->moves()->get(['player', 'x', 'y']),
        ];
    }
}
