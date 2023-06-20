/* const http = require('http');
const socketIo = require('socket.io');
const express = require('express');
const app = express();
const server = http.createServer(app);
const cors = require('cors');
app.use(cors({ origin: 'http://127.0.0.1:3000' }));
// Create a new Socket.IO instance
const io = socketIo(server);
app.get('/c1', (req, res) => {
    res.sendFile(__dirname + '/c1.html');
});
app.get('/c2', (req, res) => {
    res.sendFile(__dirname + '/c2.html');
});

// Event handler for new socket connections
io.on('connection', (socket) => {
    console.log('A new client has connected');

    // Event handler for receiving messages from clients
    socket.on('message', (data) => {
        console.log('Received message:', data);

        // Broadcast the message to all connected clients (excluding the sender)
        socket.broadcast.emit('message', data);
    });

    // Event handler for disconnections
    socket.on('disconnect', () => {
        console.log('A client has disconnected');
    });
});

// Start the server and listen on a specific port
const port = 3000; // Use any port number you prefer
server.listen(port, () => {
    console.log(`Socket.IO server is running on port ${port}`);
});
 */
// const express = require('express');
// const app = express();
// const http = require('http');
// const server = http.createServer(app);
// const { Server } = require("socket.io");
// const io = new Server(server);

// // app.get('/', (req, res) => {
// //     res.sendFile(__dirname + '/index.html');
// // });

// app.get('/c1', (req, res) => {
//     res.sendFile(__dirname + '/c1.html');
// });
// app.get('/c2', (req, res) => {
//     res.sendFile(__dirname + '/c2.html');
// });

// io.on('connection', (socket) => {
//     console.log('a user connected');
// });

// server.listen(3000, () => {
//     console.log('listening on *:3000');
// });
const express = require('express');
const app = express();
var http = require('http').Server(app);
// const server = http.createServer(app);
var io = require('socket.io')(http, {
    allowEIO3: true,
    cors: {
        origin: true,
        credentials: true
    }
});
app.get('/c1', (req, res) => {
    res.sendFile(__dirname + '/c1.html');
});
app.get('/c2', (req, res) => {
    res.sendFile(__dirname + '/c2.html');
});
// io.on('connection', (socket) => {

//     // Event handler for new socket connections
//     io.on('connection', (socket) => {
//         console.log('A new client has connected');

//         // Event handler for receiving messages from clients
//         socket.on('message', (data) => {
//             console.log('Received message:', data);

//             // Broadcast the message to all connected clients (excluding the sender)
//             io.emit('message', data);
//         });

//         // Event handler for disconnections
//         socket.on('disconnect', () => {
//             console.log('A client has disconnected');
//         });
//     })
// });

io.on('connection', (socket) => {
    console.log('A new client has connected');

    // Event handler for receiving messages from clients
    // socket.on('signin', (userId) => {
    //     const channel = `user-channel:${userId}`;
    //     socket.join(channel);
    //     console.log(`Utilisateur ${userId} connecté au canal ${channel}`);
    // });

    socket.on('chat', (data) => {

        console.log(`message ${data} `);
        io.emit('chat', data);
        // io.to(channel).emit('newMessage', message);
    }); socket.on('commande', (data) => {

        console.log(`message ${data} `);
        io.emit('commande', data);
        // io.to(channel).emit('newMessage', message);
    });
    socket.on('transaction', (data) => {

        console.log(`message ${data} `);
        io.emit('transaction', data);
        // io.to(channel).emit('newMessage', message);
    });
    socket.on('negociation', (data) => {

        console.log(`message ${data} `);
        io.emit('negociation', data);
        // io.to(channel).emit('newMessage', message);
    });

    socket.on('general', (data) => {

        console.log(`message ${data} `);
        io.emit('general', data);
        // io.to(channel).emit('newMessage', message);
    });


    // Event handler for disconnections
    socket.on('disconnect', () => {
        console.log('A client has disconnected');
    });
});
http.listen(3000, () => {
    console.log('Serveur Socket.io démarré sur le port 3000');
});
/**
 *   console.log('Nouvelle connexion :', socket.id);

        socket.on('signin', (userId) => {
            const channel = `user-channel:${userId}`;
            socket.join(channel);
            console.log(`Utilisateur ${userId} connecté au canal ${channel}`);
        });

        socket.on('message', (data) => {
            const { channel, message } = data;
            io.to(channel).emit('newMessage', message);
        });

        socket.on('disconnect', () => {
            console.log('Déconnexion :', socket.id);
        });
 */