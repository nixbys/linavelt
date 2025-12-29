const http = require('http');

const PORT = 8081; // Changed port to 8081 for testing

const server = http.createServer((req, res) => {
    if (req.url === '/health') {
        res.writeHead(200, { 'Content-Type': 'application/json' });
        res.end(JSON.stringify({ status: 'Simple HTTP server is running', timestamp: new Date() }));
    } else {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.end('Not Found');
    }
});

server.listen(PORT, '0.0.0.0', () => {
    console.log(`Simple HTTP server is attempting to bind to http://0.0.0.0:${PORT}`);
});
