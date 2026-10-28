<?php

namespace Tests\Unit;

use App\Game\TicTacToe;
use PHPUnit\Framework\TestCase;

class PruebasBasicasTest extends TestCase
{
    public function test1()
    {
        $juego = new TicTacToe();
        try {
            $juego->play(5, 1);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test2()
    {
        $juego = new TicTacToe();
        try {
            $juego->play(-1, 1);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testEjeY()
    {
        $juego = new TicTacToe();
        try {
            $juego->play(1, 5);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testEjeY2()
    {
        $juego = new TicTacToe();
        try {
            $juego->play(1, -1);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_ocupado()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        try {
            $juego->play(0, 0);
        } catch (\Exception $e) {
            $this->assertEquals('La casilla (0, 0) ya está ocupada.', $e->getMessage());
        }
    }

    public function test_ocupado2()
    {
        $juego = new TicTacToe();
        $juego->play(2, 2);
        $juego->play(1, 1);
        try {
            $juego->play(2, 2);
        } catch (\Exception $e) {
            $this->assertEquals('La casilla (2, 2) ya está ocupada.', $e->getMessage());
        }
    }

    public function testPrimerTurno()
    {
        $juego = new TicTacToe();
        $this->assertTrue($juego->nextPlayer() == 'X');
    }

    public function testPrimerTurno2()
    {
        $juego = new TicTacToe();
        $jugador = $juego->play(1, 1);
        $this->assertTrue($jugador == 'X');
    }

    public function testTurnoMas()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $this->assertEquals('+', $juego->nextPlayer());
    }

    public function testTurnoMas2()
    {
        $juego = new TicTacToe();
        $juego->play(2, 1);
        $this->assertEquals('+', $juego->nextPlayer());
    }

    public function testTurnoX()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $juego->play(0, 1);
        $this->assertEquals('X', $juego->nextPlayer());
    }

    public function testTurnoX2()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $juego->play(0, 1);
        $juego->play(0, 2);
        $juego->play(1, 0);
        $this->assertEquals('X', $juego->nextPlayer());
    }

    public function test_sin_ganador()
    {
        $juego = new TicTacToe();
        $this->assertEquals(null, $juego->winner());
    }

    public function test_sin_ganador2()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $juego->play(1, 0);
        $juego->play(2, 0);
        $this->assertEquals(null, $juego->winner());
    }

    public function testGanaHorizontal()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $juego->play(0, 1);
        $juego->play(1, 0);
        $juego->play(1, 1);
        $juego->play(2, 0);
        $this->assertEquals('X', $juego->winner());
    }

    public function testGanaHorizontal2()
    {
        $juego = new TicTacToe();
        $juego->play(0, 2);
        $juego->play(0, 1);
        $juego->play(1, 2);
        $juego->play(1, 1);
        $juego->play(2, 2);
        $this->assertEquals('X', $juego->winner());
    }

    public function testGanaVertical()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $juego->play(1, 0);
        $juego->play(0, 1);
        $juego->play(1, 1);
        $juego->play(0, 2);
        $this->assertEquals('X', $juego->winner());
    }

    public function testGanaVertical2()
    {
        $juego = new TicTacToe();
        $juego->play(2, 0);
        $juego->play(1, 0);
        $juego->play(2, 1);
        $juego->play(1, 1);
        $juego->play(2, 2);
        $this->assertEquals('X', $juego->winner());
    }

    public function testGanaDiagonal()
    {
        $juego = new TicTacToe();
        $juego->play(0, 0);
        $juego->play(1, 0);
        $juego->play(1, 1);
        $juego->play(2, 0);
        $juego->play(2, 2);
        $this->assertEquals('X', $juego->winner());
    }

    public function testGanaDiagonal2()
    {
        $juego = new TicTacToe();
        $juego->play(2, 0);
        $juego->play(0, 0);
        $juego->play(1, 1);
        $juego->play(1, 0);
        $juego->play(0, 2);
        $this->assertEquals('X', $juego->winner());
    }
}
