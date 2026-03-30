'use strict';

const express = require('express');

const PORT = process.env.MINIMAL_PORT ? parseInt(process.env.MINIMAL_PORT, 10) : 8080;
const HOST = process.env.MINIMAL_HOST || '127.0.0.1';
const app = express();

app.get('/health', (_req, res) => {
    res.json({ status: 'Minimal server is running', timestamp: new Date().toISOString() });
});

app.listen(PORT, HOST, () => {
    console.log(`Minimal server is running on http://${HOST}:${PORT}`);
});
