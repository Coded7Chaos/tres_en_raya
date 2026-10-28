import Piece from './Piece';

export default function Status({ game }) {
    if (!game) {
        return <p className="text-slate-500">Cargando…</p>;
    }

    if (game.winner) {
        return (
            <p className="flex items-center gap-2 text-lg font-semibold">
                ¡Ganó <Piece player={game.winner} className="h-6 w-6" /><span className="-ml-1.5">!</span>
            </p>
        );
    }

    if (game.is_draw) {
        return <p className="text-lg font-semibold">¡Empate!</p>;
    }

    return (
        <p className="flex items-center gap-2 text-lg font-medium text-slate-600">
            Turno de <Piece player={game.next_player} className="h-6 w-6" />
        </p>
    );
}
