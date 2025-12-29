const express = require('express');
const app = express();
const PORT = 8080; // Change port to 8080 for testing

app.get('/health', (req, res) => {
    res.json({ status: 'Minimal server is running', timestamp: new Date() });
});

app.listen(PORT, '0.0.0.0', () => {
    console.log(`Minimal server is attempting to bind to http://0.0.0.0:${PORT}`);
});
