import Piece from './Piece';

function ScoreCard({ label, value, icon, active }) {
    return (
        <div
            className={`flex flex-1 flex-col items-center gap-1 rounded-2xl bg-white px-3 py-3 shadow-sm transition ${
                active ? 'ring-2 ring-slate-800' : ''
            }`}
        >
            <div className="flex h-6 items-center gap-1.5 text-xs font-medium tracking-wide text-slate-500 uppercase">
                {icon}
                {label}
            </div>
            <div className="text-2xl font-bold tabular-nums">{value}</div>
        </div>
    );
}

export default function Scoreboard({ stats, nextPlayer }) {
    return (
        <div className="flex gap-3">
            <ScoreCard
                label="Jugador"
                value={stats?.x_wins ?? 0}
                icon={<Piece player="X" className="h-4 w-4" />}
                active={nextPlayer === 'X'}
            />
            <ScoreCard label="Empates" value={stats?.draws ?? 0} />
            <ScoreCard
                label="Jugador"
                value={stats?.plus_wins ?? 0}
                icon={<Piece player="+" className="h-4 w-4" />}
                active={nextPlayer === '+'}
            />
        </div>
    );
}
