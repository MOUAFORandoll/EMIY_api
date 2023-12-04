// const express = require('express');
// const app = express(); const fs = require('fs');

// const http = require('http').createServer(app);
// const io = require('socket.io')(http, {
//     cors: {
//         origin: true,
//         credentials: true
//     }
// });
// let i = 0;
// app.get('/c1', (req, res) => {
//     save(i);
//     i++;
//     res.sendFile(__dirname + '/c1.html');
// });

// app.get('/c2', (req, res) => {
//     read('000');
//     res.sendFile(__dirname + '/c2.html');
// });

// io.on('connection', (socket) => {
//     console.log('A new client has connected',);

//     // Event handler for receiving chat messages from clients
//     socket.on('chat', (data) => {
//         console.log(`Chat message received: ${data}`);
//         io.emit('chat', data);
//     });

//     // Event handler for receiving command messages from clients
//     socket.on('commande', (data) => {
//         console.log(`Command message received: ${data}`);
//         io.emit('commande', data);
//     });

//     // Event handler for receiving transaction messages from clients
//     socket.on('transaction', (data) => {
//         console.log(`Transaction message received: ${data}`);
//         io.emit('transaction', data);
//     });

//     // Event handler for receiving negotiation messages from clients
//     socket.on('negociation', (data) => {
//         console.log(`Negotiation message received: ${data}`);
//         io.emit('negociation', data);
//     });

//     // Event handler for receiving general messages from clients
//     socket.on('general', (data) => {
//         console.log(`General message received: ${data}`);
//         io.emit('general', data);
//     });

//     // Event handler for receiving service client messages from clients
//     socket.on('service_client', (data) => {
//         console.log(JSON.parse(data));
//         if (save(JSON.parse(data)['data']['id'])) {

//             console.log(`Service client message received: ${data}`);
//             io.emit('service_client', data);
//         } else {
//             console.log(`  client message existe dje: ${data}`);
//         }
//     });

//     // Event handler for receiving notifications messages from clients
//     socket.on('notifications', (data) => {
//         console.log(`notifications message received: ${data}`);
//         io.emit('notifications', data);
//     });

//     // Event handler for disconnections
//     socket.on('disconnect', () => {
//         console.log('A client has disconnected');
//     });
// });
// function save(data) {
//     console.log(exist(data));
//     if (!exist(data)) {

//         // Écrire des données dans un fichier

//         fs.writeFile('data.txt', read() + ';' + data, (err) => {
//             if (err) {
//                 console.error('Error writing file:', err);
//                 return;
//             }
//             // console.log('Data has been stored in data.txt');
//         });
//         return true;
//     }
//     return false;

// }
// var datas = '';
// function read() {

//     // Lire des données à partir d'un fichier
//     fs.readFile('data.txt', 'utf8', (err, data) => {
//         if (err) {
//             console.error('Error reading file:', err);
//             return;
//         }
//         datas = data;

//     });
//     console.log('Data read from file:', datas);
//     return datas;

// }

// function exist(elt) {

//     // Lire des données à partir d'un fichier
//     fs.readFile('data.txt', 'utf8', (err, data) => {
//         if (err) {
//             console.error('Error reading file:', err);
//             return;
//         }
//         datas = data;

//     });
//     console.log('Data read from file:', datas.split(';').includes(elt.toString()));
//     return datas.split(';').includes(elt.toString());

// }
// http.listen(3000, () => {
//     console.log('Socket.IO server is running on port 3000');
// });

const express = require("express");
const app = express();
const http = require("http").createServer(app);
const io = require("socket.io")(http, {
  cors: {
    origin: true,
    credentials: true,
  },
});

let i = 0;

app.get("/c1", (req, res) => {
  save(i);
  i++;
  res.sendFile(__dirname + "/c1.html");
});

app.get("/c2", (req, res) => {
  read("000");
  res.sendFile(__dirname + "/c2.html");
});

io.on("connection", (socket) => {
  console.log("A new client has connected");

  const eventHandlers = [
    "chat",
    "commande",
    "transaction",
    "negociation",
    "general",
    "service_client",
    "notifications",
  ];

  eventHandlers.forEach((event) => {
    socket.on(event, (data) => {
      console.log(`${event} message received: ${data}`);
      if (event === "service_client") {
        const jsonData = JSON.parse(data);
        if (!exist(jsonData["data"]["id"])) {
          console.log(`Service client ${event}  message received: ${data}`);
          io.emit(event, data);
        } else {
          console.log(`Client message already exists: ${data}`);
        }
      } else {
        io.emit(event, data);
      }
    });
  });

  socket.on("disconnect", () => {
    console.log("A client has disconnected");
  });
});

http.listen(3000, () => {
  console.log("Socket.IO server is running on port 3000");
});
