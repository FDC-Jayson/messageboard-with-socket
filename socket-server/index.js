
const express = require('express');
const http = require('http');

const cors = require('cors'); // Import the cors library

const app = express();
const server = http.createServer(app);

const io = require('socket.io')(server, { 
    cors: {
        origins: '*:*'
    }
});

io.on('connection', (socket) => {
    console.log('A user connected');

    // Handle incoming messages from clients
    socket.on('message', (data) => {
        // Save the message to the database
        console.log(data)
        // Broadcast the message to all connected clients
        io.emit('message', data);
    });

    socket.on('remove_message', (data) => {
        // Save the message to the database
        console.log(data)
        // Broadcast the message to all connected clients
        io.emit('remove_message', data);
    });

    socket.on('disconnect', () => {
        console.log('A user disconnected');
    });
});

server.listen(3000, () => {
    console.log('Socket.IO server listening on port 3000');
});
