import fs from "fs";
import express from "express";

import { extname, resolve } from "path";
import { promisify } from "util";
const app = express();
const port = 4000;

// Configuration pour activer les éventuels CORs (Cross-Origin Resource Sharing)
app.use((request, response, next) => {
  response.header("Access-Control-Allow-Origin", "*");
  next();
});

// Route pour diffuser la vidéo
app.use(async (request, response, next) => {
  console.log(!request.query.video);
  if (
    !request.url.startsWith("/short") ||
    !request.query
      .video /* || !request.query.video.match(/^[a-z0-9-_] +\.(mp4)$/i)  */
  ) {
    console.log(request.url);
    return next();
  }
  if (
    request.url.startsWith("/short") ||
    !request.query
      .video /* || !request.query.video.match(/^[a-z0-9-_] +\.(mp4)$/i)  */
  ) {
    const video = resolve("../public/videos/shorts", request.query.video);

    // Définir le chemin de votre vidéo
    const videoPath = "../public/videos/shorts/" + request.query.video;
    try {
      const range = request.headers.range;
      const videoSize = fs.statSync(videoPath).size;

      if (range) {
        const parts = range.replace(/bytes=/, "").split("-");
        const start = parseInt(parts[0], 10);
        const end = parts[1] ? parseInt(parts[1], 10) : videoSize - 1;
        const chunksize = end - start + 1;
        const file = fs.createReadStream(videoPath, { start, end });
        const head = {
          "Content-Range": `bytes ${start}-${end}/${videoSize}`,
          "Accept-Ranges": "bytes",
          "Content-Length": chunksize,
          "Content-Type": "video/mp4",
        };
        response.writeHead(206, head);
        file.pipe(response);
        console.log("00");
        console.log(head);
      } else {
        const head = {
          "Content-Length": videoSize,
          "Content-Type": "video/mp4",
        };
        response.writeHead(200, head);
        fs.createReadStream(videoPath).pipe(response);
        console.log("11");
      }
    } catch (error) {
      if (error.code === "ECONNRESET" && retryCount < 5) {
        // Essayer de se reconnecter jusqu'à 5 fois
        retryCount++;
        console.log(`Reconnecting... Attempt ${retryCount}`);

        // Réessayer la connexion après un délai (par exemple, 1 seconde)
        setTimeout(() => {
          return next();
        }, 1000);
      } else {
        const head = {
          "Content-Length": 0,
          "Content-Type": "video/mp4",
        };
        response.writeHead(500, head);
      }
    }
  }
});

app.listen(port, () => {
  console.log(`Serveur de streaming actif sur http://localhost:${port}`);
});
