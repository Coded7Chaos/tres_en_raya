export const PLAYER_STYLES = {
    X: { text: 'text-indigo-500', bg: 'bg-indigo-50', ring: 'ring-indigo-200' },
    '+': { text: 'text-rose-500', bg: 'bg-rose-50', ring: 'ring-rose-200' },
};

export default function Piece({ player, className = '' }) {
    const d = player === 'X' ? 'M6 6 L18 18 M18 6 L6 18' : 'M12 4 L12 20 M4 12 L20 12';

    return (
        <svg
            viewBox="0 0 24 24"
            className={`${PLAYER_STYLES[player].text} ${className}`}
            fill="none"
            stroke="currentColor"
            strokeWidth="3"
            strokeLinecap="round"
            aria-label={player}
        >
            <path d={d} />
        </svg>
    );
}
