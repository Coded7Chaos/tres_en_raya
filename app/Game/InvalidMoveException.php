<?php

namespace App\Game;

use DomainException;

class InvalidMoveException extends DomainException
{
    public static function outOfXAxis(int $x): self
    {
        return new self("La posición X={$x} está fuera del tablero (debe estar entre 0 y ".(TicTacToe::SIZE - 1).').');
    }

    public static function outOfYAxis(int $y): self
    {
        return new self("La posición Y={$y} está fuera del tablero (debe estar entre 0 y ".(TicTacToe::SIZE - 1).').');
    }

    public static function occupied(int $x, int $y): self
    {
        return new self("La casilla ({$x}, {$y}) ya está ocupada.");
    }

    public static function gameOver(): self
    {
        return new self('La partida ya terminó.');
    }
}
