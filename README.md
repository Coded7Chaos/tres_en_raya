# Tres en Raya

Juego de tres en raya (X contra +) hecho con Laravel 13, React 19, Tailwind CSS 4 y MySQL.

## Requisitos

- PHP 8.3+ y Composer
- Node.js 20+
- MySQL corriendo en `127.0.0.1:3306` (usuario `root` sin contraseña por defecto; cámbialo en `.env`)

## Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
mysql -uroot -e "CREATE DATABASE IF NOT EXISTS tres_en_raya"
php artisan migrate
```

## Ejecutar

```bash
composer run dev
```

Abre http://localhost:8000. (`composer run dev` levanta el servidor de Laravel y Vite a la vez.)

## Pruebas

```bash
php artisan test
```

Las pruebas del motor están en `tests/Unit/TicTacToeTest.php` y cubren todos los requerimientos:

| Requerimiento | Pruebas |
| --- | --- |
| Poner una pieza en el tablero 3x3 | excepción fuera del eje X, fuera del eje Y y en casilla ocupada |
| Saber quién juega después | el primer turno es de `X`; después de `X` va `+`; después de `+` va `X` |
| Condición de victoria | sin línea no hay ganador; gana con línea horizontal, vertical y diagonal |

`tests/Feature/GameApiTest.php` prueba la API y que las partidas se guarden en la base de datos.

## Estructura

- `app/Game/TicTacToe.php`: las reglas del juego (sin dependencias de Laravel). Coordenadas `x` (columna) e `y` (fila) de 0 a 2.
- `app/Game/InvalidMoveException.php`: la excepción que se lanza ante una jugada inválida.
- `app/Models/Game.php`, `app/Models/Move.php`: la partida y sus jugadas en MySQL. El estado se reconstruye reproduciendo las jugadas con el motor.
- `app/Http/Controllers/GameController.php` y `routes/api.php`: la API JSON.
- `resources/js/`: el frontend en React (`app.jsx` y `components/`).

## API

| Método | Ruta | Descripción |
| --- | --- | --- |
| `POST` | `/api/games` | Crea una partida nueva |
| `GET` | `/api/games/{id}` | Estado de una partida |
| `POST` | `/api/games/{id}/moves` | Juega en `{ "x": 0-2, "y": 0-2 }`; responde 422 si la jugada es inválida |
| `GET` | `/api/stats` | Victorias de cada jugador, empates y últimas partidas |
