import Koa from "koa";
import { createReadStream, stat } from "fs";
import { extname, resolve } from "path";
import { promisify } from "util";
const app = new Koa();

let retryCount = 0;
app.use(async ({ request, response }, next) => {
    console.log(!request.url.startsWith('/api/video'));
    console.log(!request.query.video);
    if (!request.url.startsWith('/short') || !request.query.video /* || !request.query.video.match(/^[a-z0-9-_] +\.(mp4)$/i)  */) {
        console.log(request.url);
        return next();
    }
    if (request.url.startsWith('/short') || !request.query.video /* || !request.query.video.match(/^[a-z0-9-_] +\.(mp4)$/i)  */) {

        const video = resolve('../public/videos/shorts', request.query.video)
        console.log(video);
        const range = request.header.range
        if (!range) {


            response.type = extname(video);
            response.body = createReadStream(video)
            return next();
        }
        const videoStat = await promisify(stat)(video);
        const videoSize = videoStat.size
        const CHUNK_SIZE = 10 ** 6;
        const start = Number(range.replace(/\D/g, ""));
        const end = Math.min(start + CHUNK_SIZE, videoSize - 1);
        const contentLength = end - start + 1;
        response.set('Content-Range', `bytes ${start}-${end}/${videoSize}`)
        response.set('Accept-Range', `bytes`)
        response.set('Content-Length', contentLength)
        response.status = 206
        response.body = createReadStream(video, { start, end })

        console.log(start, end)
    }
})

app.on('error', async (error, ctx) => {

    if (error.code === "ECONNRESET") {
        retryCount++;
        console.log(`Reconnecting... Attempt ${retryCount}`);

    }
})
app.listen(4000)
// import Koa from "koa";
// import { createReadStream, stat } from "fs";
// import { extname, resolve } from "path";
// import { promisify } from "util";

// const app = new Koa();

// let retryCount = 0;

// app.use(async (ctx, next) => {
//     if (!ctx.request.url.startsWith('/short') || !ctx.request.query.video) {
//         return next();
//     }
//     console.log(ctx.request.query.video);
//     const video = resolve('../public/videos/shorts', ctx.request.query.video);

//     try {

//         const videoStat = await promisify(stat)(video);
//         const videoSize = videoStat.size;

//         const range = ctx.request.headers.range;
//         if (!range) {
//             ctx.response.type = extname(video);
//             ctx.response.body = createReadStream(video);
//             return;
//         }

//         const parts = range.replace('bytes=', '').split('-');
//         const start = Number(parts[0]);
//         const end = parts[1] ? parseInt(parts[1], 10) : videoSize - 1;
//         const contentLength = end - start + 1;

//         ctx.response.set('Content-Range', `bytes ${start}-${end}/${videoSize}`);
//         ctx.response.set('Accept-Range', 'bytes');
//         ctx.response.set('Content-Length', contentLength);
//         ctx.response.status = 206;
//         ctx.response.body = createReadStream(video, { start, end });

//         console.log(start, end);
//     } catch (error) {
//         console.error(error);
//         ctx.response.status = 500;
//         ctx.response.body = 'Internal Server Error';
//     }
// });

// app.on('error', async (error, ctx) => {
//     if (error.code === "ECONNRESET") {
//         retryCount++;
//         console.log(`Reconnecting... Attempt ${retryCount}`);
//     }
// });

// app.listen(4000);
