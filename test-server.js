import http from 'http';

const PORT = 9090;
const HOST = '127.0.0.1';

const server = http.createServer((_req, res) => {
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    res.end('Server is running');
});

server.listen(PORT, HOST, () => {
    console.log(`Test server is listening on http://${HOST}:${PORT}`);
});
