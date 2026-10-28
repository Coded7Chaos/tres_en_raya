<?php

namespace Tests\Unit;

use App\Game\InvalidMoveException;
use App\Game\TicTacToe;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TicTacToeTest extends TestCase
{
    // --- Requerimiento 1: poner una pieza en un tablero 3x3 ---

    #[Test]
    public function un_jugador_puede_poner_una_pieza_en_el_tablero(): void
    {
        $game = new TicTacToe;

        $game->play(1, 2);

        $this->assertSame(TicTacToe::X, $game->board()[2][1]);
    }

    #[Test]
    #[DataProvider('posicionesFueraDelEjeX')]
    public function lanza_excepcion_cuando_la_pieza_esta_fuera_del_eje_x(int $x): void
    {
        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage("X={$x}");

        (new TicTacToe)->play($x, 1);
    }

    public static function posicionesFueraDelEjeX(): array
    {
        return ['menor que 0' => [-1], 'mayor que 2' => [3]];
    }

    #[Test]
    #[DataProvider('posicionesFueraDelEjeY')]
    public function lanza_excepcion_cuando_la_pieza_esta_fuera_del_eje_y(int $y): void
    {
        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage("Y={$y}");

        (new TicTacToe)->play(1, $y);
    }

    public static function posicionesFueraDelEjeY(): array
    {
        return ['menor que 0' => [-1], 'mayor que 2' => [3]];
    }

    #[Test]
    public function lanza_excepcion_cuando_la_pieza_esta_en_un_lugar_ocupado(): void
    {
        $game = new TicTacToe;
        $game->play(1, 1);

        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage('ya está ocupada');

        $game->play(1, 1);
    }

    // --- Requerimiento 2: determinar qué jugador juega después ---

    #[Test]
    public function el_primer_turno_siempre_es_de_x(): void
    {
        $game = new TicTacToe;

        $this->assertSame(TicTacToe::X, $game->nextPlayer());
        $this->assertSame(TicTacToe::X, $game->play(0, 0));
    }

    #[Test]
    public function si_el_ultimo_turno_fue_de_x_el_siguiente_es_de_mas(): void
    {
        $game = new TicTacToe;
        $game->play(0, 0);

        $this->assertSame(TicTacToe::PLUS, $game->nextPlayer());
        $this->assertSame(TicTacToe::PLUS, $game->play(1, 0));
    }

    #[Test]
    public function si_el_ultimo_turno_fue_de_mas_el_siguiente_es_de_x(): void
    {
        $game = new TicTacToe;
        $game->play(0, 0);
        $game->play(1, 0);

        $this->assertSame(TicTacToe::X, $game->nextPlayer());
        $this->assertSame(TicTacToe::X, $game->play(2, 0));
    }

    // --- Requerimiento 3: condiciones de victoria ---

    #[Test]
    public function si_no_se_cumple_la_condicion_de_victoria_no_hay_ganador(): void
    {
        $game = new TicTacToe;
        $this->assertNull($game->winner());

        $this->playMoves($game, x: [[0, 0], [2, 0], [1, 1]], plus: [[1, 0], [0, 1]]);

        $this->assertNull($game->winner());
        $this->assertFalse($game->isOver());
    }

    #[Test]
    public function un_tablero_lleno_sin_lineas_es_empate_sin_ganador(): void
    {
        $game = new TicTacToe;

        // X | + | X
        // X | + | +
        // + | X | X
        $this->playMoves(
            $game,
            x: [[0, 0], [2, 0], [0, 1], [1, 2], [2, 2]],
            plus: [[1, 0], [1, 1], [2, 1], [0, 2]],
        );

        $this->assertNull($game->winner());
        $this->assertTrue($game->isDraw());
    }

    #[Test]
    #[DataProvider('filas')]
    public function un_jugador_gana_con_una_linea_horizontal(int $y): void
    {
        $game = new TicTacToe;
        $otherRow = ($y + 1) % 3;

        $this->playMoves($game, x: [[0, $y], [1, $y], [2, $y]], plus: [[0, $otherRow], [1, $otherRow]]);

        $this->assertSame(TicTacToe::X, $game->winner());
        $this->assertSame([[0, $y], [1, $y], [2, $y]], $game->winningLine());
    }

    public static function filas(): array
    {
        return ['fila superior' => [0], 'fila del medio' => [1], 'fila inferior' => [2]];
    }

    #[Test]
    #[DataProvider('columnas')]
    public function un_jugador_gana_con_una_linea_vertical(int $x): void
    {
        $game = new TicTacToe;
        $otherColumn = ($x + 1) % 3;

        $this->playMoves($game, x: [[$x, 0], [$x, 1], [$x, 2]], plus: [[$otherColumn, 0], [$otherColumn, 1]]);

        $this->assertSame(TicTacToe::X, $game->winner());
        $this->assertSame([[$x, 0], [$x, 1], [$x, 2]], $game->winningLine());
    }

    public static function columnas(): array
    {
        return ['columna izquierda' => [0], 'columna del medio' => [1], 'columna derecha' => [2]];
    }

    #[Test]
    public function un_jugador_gana_con_la_diagonal_principal(): void
    {
        $game = new TicTacToe;

        $this->playMoves($game, x: [[0, 0], [1, 1], [2, 2]], plus: [[1, 0], [2, 0]]);

        $this->assertSame(TicTacToe::X, $game->winner());
    }

    #[Test]
    public function un_jugador_gana_con_la_diagonal_secundaria(): void
    {
        $game = new TicTacToe;

        $this->playMoves($game, x: [[2, 0], [1, 1], [0, 2]], plus: [[0, 0], [1, 0]]);

        $this->assertSame(TicTacToe::X, $game->winner());
    }

    #[Test]
    public function el_jugador_mas_tambien_puede_ganar(): void
    {
        $game = new TicTacToe;

        $this->playMoves($game, x: [[0, 0], [1, 0], [2, 2]], plus: [[0, 1], [1, 1], [2, 1]]);

        $this->assertSame(TicTacToe::PLUS, $game->winner());
    }

    #[Test]
    public function no_se_puede_jugar_despues_de_que_alguien_gano(): void
    {
        $game = new TicTacToe;
        $this->playMoves($game, x: [[0, 0], [1, 0], [2, 0]], plus: [[0, 1], [1, 1]]);

        $this->expectException(InvalidMoveException::class);
        $this->expectExceptionMessage('terminó');

        $game->play(2, 2);
    }

    /**
     * Alterna las jugadas empezando por X: x[0], plus[0], x[1], plus[1], ...
     */
    private function playMoves(TicTacToe $game, array $x, array $plus): void
    {
        foreach ($x as $i => [$px, $py]) {
            $game->play($px, $py);

            if (isset($plus[$i])) {
                $game->play(...$plus[$i]);
            }
        }
    }
}
