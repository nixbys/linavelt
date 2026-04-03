'use strict';

const http = require('http');

const PORT = process.env.SIMPLE_PORT ? parseInt(process.env.SIMPLE_PORT, 10) : 8081;
const HOST = process.env.SIMPLE_HOST || '127.0.0.1';

const server = http.createServer((_req, res) => {
    if (_req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'Simple HTTP server is running', timestamp: new Date().toISOString() }));
    } else {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('Not Found');
    }
});

server.listen(PORT, HOST, () => {
    console.log(`Simple HTTP server is running on http://${HOST}:${PORT}`);
});
