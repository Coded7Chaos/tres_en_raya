<?php

namespace App\Game;

/**
 * Reglas del Tres en Raya. El tablero se indexa como $board[$y][$x],
 * con X (columna) e Y (fila) entre 0 y 2.
 */
class TicTacToe
{
    public const SIZE = 3;

    public const X = 'X';

    public const PLUS = '+';

    /** @var array<int, array<int, string|null>> */
    private array $board;

    private ?string $lastPlayer = null;

    public function __construct()
    {
        $this->board = array_fill(0, self::SIZE, array_fill(0, self::SIZE, null));
    }

    /**
     * Reconstruye una partida a partir de una lista de jugadas [['x' => .., 'y' => ..], ...].
     */
    public static function fromMoves(iterable $moves): self
    {
        $game = new self;

        foreach ($moves as $move) {
            $game->play((int) $move['x'], (int) $move['y']);
        }

        return $game;
    }

    /**
     * Coloca la pieza del jugador de turno y devuelve qué jugador jugó.
     *
     * @throws InvalidMoveException
     */
    public function play(int $x, int $y): string
    {
        if ($x < 0 || $x >= self::SIZE) {
            throw InvalidMoveException::outOfXAxis($x);
        }

        if ($y < 0 || $y >= self::SIZE) {
            throw InvalidMoveException::outOfYAxis($y);
        }

        if ($this->board[$y][$x] !== null) {
            throw InvalidMoveException::occupied($x, $y);
        }

        if ($this->isOver()) {
            throw InvalidMoveException::gameOver();
        }

        $player = $this->nextPlayer();
        $this->board[$y][$x] = $player;
        $this->lastPlayer = $player;

        return $player;
    }

    public function nextPlayer(): string
    {
        return $this->lastPlayer === self::X ? self::PLUS : self::X;
    }

    public function winner(): ?string
    {
        $line = $this->winningLine();

        return $line === null ? null : $this->board[$line[0][1]][$line[0][0]];
    }

    /**
     * Coordenadas [x, y] de la línea ganadora, o null si nadie ha ganado.
     *
     * @return array<int, array{0: int, 1: int}>|null
     */
    public function winningLine(): ?array
    {
        foreach (self::lines() as $line) {
            [$a, $b, $c] = array_map(fn (array $p) => $this->board[$p[1]][$p[0]], $line);

            if ($a !== null && $a === $b && $b === $c) {
                return $line;
            }
        }

        return null;
    }

    public function isDraw(): bool
    {
        return $this->winner() === null && $this->isFull();
    }

    public function isOver(): bool
    {
        return $this->winner() !== null || $this->isFull();
    }

    /**
     * @return array<int, array<int, string|null>>
     */
    public function board(): array
    {
        return $this->board;
    }

    private function isFull(): bool
    {
        foreach ($this->board as $row) {
            if (in_array(null, $row, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Todas las líneas posibles: horizontales, verticales y las dos diagonales.
     *
     * @return array<int, array<int, array{0: int, 1: int}>>
     */
    private static function lines(): array
    {
        $lines = [];
        $range = range(0, self::SIZE - 1);

        foreach ($range as $i) {
            $lines[] = array_map(fn ($x) => [$x, $i], $range);
            $lines[] = array_map(fn ($y) => [$i, $y], $range);
        }

        $lines[] = array_map(fn ($i) => [$i, $i], $range);
        $lines[] = array_map(fn ($i) => [$i, self::SIZE - 1 - $i], $range);

        return $lines;
    }
}
