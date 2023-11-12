import fs from "fs";
import express from "express";

const app = express();
const port = 3000;

// Définir le chemin de votre vidéo
const videoPath = "video/test.mp4";

// Configuration pour activer les éventuels CORs (Cross-Origin Resource Sharing)
app.use((req, res, next) => {
  res.header("Access-Control-Allow-Origin", "*");
  next();
});

// Route pour diffuser la vidéo
app.get("/video", (req, res) => {
  const range = req.headers.range;
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
    res.writeHead(206, head);
    file.pipe(res);
    console.log("00");
    console.log(head);
   
  } else {
    const head = {
      "Content-Length": videoSize,
      "Content-Type": "video/mp4",
    };
    res.writeHead(200, head);
    fs.createReadStream(videoPath).pipe(res);
    console.log("11");
  }
});

app.listen(port, () => {
  console.log(`Serveur de streaming actif sur http://localhost:${port}`);
});
