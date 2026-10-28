import Piece, { PLAYER_STYLES } from './Piece';

export default function Board({ board, winningLine, nextPlayer, disabled, onPlay }) {
    const isWinningCell = (x, y) => winningLine?.some(([wx, wy]) => wx === x && wy === y);

    return (
        <div className="grid grid-cols-3 gap-3 rounded-3xl bg-slate-200/70 p-3">
            {board.map((row, y) =>
                row.map((cell, x) => {
                    const winning = isWinningCell(x, y);

                    return (
                        <button
                            key={`${x}-${y}`}
                            type="button"
                            onClick={() => onPlay(x, y)}
                            disabled={disabled}
                            aria-label={`Casilla ${x}, ${y}${cell ? `: ${cell}` : ''}`}
                            className={[
                                'group relative flex aspect-square items-center justify-center rounded-2xl bg-white shadow-sm transition',
                                'enabled:hover:-translate-y-0.5 enabled:hover:shadow-md disabled:cursor-default',
                                winning ? `${PLAYER_STYLES[cell].bg} ring-4 ${PLAYER_STYLES[cell].ring}` : '',
                                winningLine && !winning ? 'opacity-50' : '',
                            ].join(' ')}
                        >
                            {cell ? (
                                <Piece player={cell} className="animate-pop h-3/5 w-3/5" />
                            ) : (
                                !disabled &&
                                nextPlayer && (
                                    <Piece
                                        player={nextPlayer}
                                        className="h-3/5 w-3/5 opacity-0 transition group-hover:opacity-20"
                                    />
                                )
                            )}
                        </button>
                    );
                }),
            )}
        </div>
    );
}
