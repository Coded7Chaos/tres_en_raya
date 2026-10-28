<?php

namespace App\Http\Controllers;

use App\Game\InvalidMoveException;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function store(): JsonResponse
    {
        return response()->json(Game::create()->toState(), 201);
    }

    public function show(Game $game): JsonResponse
    {
        return response()->json($game->toState());
    }

    public function move(Request $request, Game $game): JsonResponse
    {
        $data = $request->validate([
            'x' => ['required', 'integer'],
            'y' => ['required', 'integer'],
        ]);

        try {
            $engine = $game->play($data['x'], $data['y']);
        } catch (InvalidMoveException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($game->toState($engine));
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'x_wins' => Game::where('winner', 'X')->count(),
            'plus_wins' => Game::where('winner', '+')->count(),
            'draws' => Game::where('status', 'draw')->count(),
            'recent' => Game::whereIn('status', ['won', 'draw'])
                ->latest('updated_at')
                ->limit(5)
                ->get(['id', 'status', 'winner', 'updated_at']),
        ]);
    }
}
