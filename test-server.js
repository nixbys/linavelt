import http from 'http';

const PORT = 9090;

const server = http.createServer((req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('Server is running');
});

server.listen(PORT, '0.0.0.0', () => {
    console.log(`Test server is listening on http://0.0.0.0:${PORT}`);
});
