import { StrictMode, useCallback, useEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { api } from './api';
import Board from './components/Board';
import Scoreboard from './components/Scoreboard';
import Status from './components/Status';

const STORAGE_KEY = 'tres-en-raya:game';

function rememberGame(id) {
    try {
        localStorage.setItem(STORAGE_KEY, id);
    } catch {
        // Sin almacenamiento local simplemente no se retoma la partida.
    }
}

function savedGameId() {
    try {
        return localStorage.getItem(STORAGE_KEY);
    } catch {
        return null;
    }
}

function App() {
    const [game, setGame] = useState(null);
    const [stats, setStats] = useState(null);
    const [error, setError] = useState(null);
    const [busy, setBusy] = useState(false);
    const loaded = useRef(false);

    const refreshStats = useCallback(() => api.stats().then(setStats), []);

    const startNewGame = useCallback(async () => {
        setError(null);
        const created = await api.newGame();
        rememberGame(created.id);
        setGame(created);
    }, []);

    useEffect(() => {
        // StrictMode ejecuta el efecto dos veces en desarrollo; evitamos crear dos partidas.
        if (loaded.current) return;
        loaded.current = true;

        const id = savedGameId();
        (id ? api.getGame(id).catch(() => null) : Promise.resolve(null)).then((saved) =>
            saved && !saved.is_over ? setGame(saved) : startNewGame(),
        );
        refreshStats();
    }, [startNewGame, refreshStats]);

    async function play(x, y) {
        if (busy || game.is_over) return;

        setBusy(true);
        setError(null);

        try {
            const updated = await api.play(game.id, x, y);
            setGame(updated);
            if (updated.is_over) refreshStats();
        } catch (e) {
            setError(e.message);
        } finally {
            setBusy(false);
        }
    }

    return (
        <main className="mx-auto flex min-h-screen max-w-md flex-col justify-center gap-6 px-4 py-10">
            <header className="text-center">
                <h1 className="text-4xl font-bold tracking-tight">Tres en Raya</h1>
                <p className="mt-1 text-sm text-slate-500">X contra +. Conecta tres para ganar.</p>
            </header>

            <Scoreboard stats={stats} nextPlayer={game?.next_player} />

            <section className="flex flex-col gap-4 rounded-[2rem] bg-white/60 p-4 shadow-xl shadow-slate-300/40 backdrop-blur">
                <div className="flex min-h-8 items-center justify-center">
                    <Status game={game} />
                </div>

                {game && (
                    <Board
                        board={game.board}
                        winningLine={game.winning_line}
                        nextPlayer={game.next_player}
                        disabled={game.is_over || busy}
                        onPlay={play}
                    />
                )}

                <p
                    role="alert"
                    className={`min-h-5 text-center text-sm text-rose-600 transition ${error ? 'opacity-100' : 'opacity-0'}`}
                >
                    {error}
                </p>

                <button
                    type="button"
                    onClick={startNewGame}
                    className={`rounded-2xl px-5 py-3 font-semibold transition active:scale-[0.98] ${
                        game?.is_over
                            ? 'bg-slate-900 text-white shadow-lg hover:bg-slate-700'
                            : 'bg-slate-200 text-slate-700 hover:bg-slate-300'
                    }`}
                >
                    {game?.is_over ? 'Jugar otra vez' : 'Reiniciar partida'}
                </button>
            </section>

            {stats?.recent?.length > 0 && (
                <footer className="text-center text-xs text-slate-400">
                    Últimas partidas:{' '}
                    {stats.recent.map((g) => (g.status === 'draw' ? 'empate' : g.winner)).join(' · ')}
                </footer>
            )}
        </main>
    );
}

createRoot(document.getElementById('app')).render(
    <StrictMode>
        <App />
    </StrictMode>,
);
