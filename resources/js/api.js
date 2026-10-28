async function request(method, url, body) {
    const response = await fetch(`/api${url}`, {
        method,
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
        body: body ? JSON.stringify(body) : undefined,
    });

    const data = await response.json();

    if (!response.ok) {
        throw new Error(data.message ?? 'Algo salió mal.');
    }

    return data;
}

export const api = {
    newGame: () => request('POST', '/games'),
    getGame: (id) => request('GET', `/games/${id}`),
    play: (id, x, y) => request('POST', `/games/${id}/moves`, { x, y }),
    stats: () => request('GET', '/stats'),
};
