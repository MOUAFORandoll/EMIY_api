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
        try {
            console.log('--------------------------video');
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
        } catch (error) {
            if (error.code === "ECONNRESET" && retryCount < 5) { // Essayer de se reconnecter jusqu'à 5 fois
                retryCount++;
                console.log(`Reconnecting... Attempt ${retryCount}`);

                // Réessayer la connexion après un délai (par exemple, 1 seconde)
                setTimeout(() => {
                    return next();
                }, 1000);
            } else {
                console.error(error);
                ctx.status = 500;
                ctx.body = 'Internal Server Error';
            }
        }
    }
})

// app.on('error', async (error, ctx) => {

//     if (error.code === "ECONNRESET") {
//         retryCount++;
//         console.log(`Reconnecting... Attempt ${retryCount}`);

//     }
// })

app.on('error', async (error, ctx) => {
    if (error.code === "ECONNRESET") {
        retryCount++;
        console.log(`Reconnecting... Attempt ${retryCount}`);

        // Réessayer la connexion après un délai (par exemple, 1 seconde)
        // setTimeout(() => {
        //     ctx.res.end(); // Terminer la réponse actuelle pour éviter les problèmes de réponse en double
        //     ctx.respond = false; // Désactiver la réponse par défaut pour éviter les problèmes de double réponse
        //     if (ctx.state.range) {
        //         ctx.req.headers.range = `bytes=${ctx.state.range.start}-`; // Réinitialiser la plage pour la prochaine requête
        //     }
        //     app.handleRequest(ctx.req, ctx.res); // Réessayer la requête
        // }, 1000);
    }
});






app.listen(4000)



// import Koa from "koa";
// import { createReadStream, stat } from "fs";
// import { extname, resolve } from "path";
// import { promisify } from "util";
// import range from "koa-range"; // Assurez-vous que la bibliothèque est correctement importée
// let retryCount = 0;
// const app = new Koa();

// app.use(range);

// app.use(async (ctx, next) => {
//     if (!ctx.url.startsWith('/short') || !ctx.query.video) {
//         return next();
//     }

//     // Utilisez ctx.range pour accéder à la plage définie par la bibliothèque koa-range
//     const { start, end } = ctx.range || { start: 0, end: undefined };
//     console.log('rabge', range.arguments(ctx));


//     const video = resolve('../public/videos/shorts', ctx.query.video);

//     try {
//         const videoStat = await promisify(stat)(video);
//         const videoSize = videoStat.size;
//         console.log(start, end);

//         ctx.set('Content-Type', extname(video));
//         ctx.set('Content-Length', end ? end - start + 1 : videoSize - start);
//         ctx.set('Accept-Ranges', 'bytes');
//         ctx.set('Content-Range', `bytes ${start}-${end ? end : videoSize - 1}/${videoSize}`);
//         ctx.status = 206;
//         ctx.body = createReadStream(video, { start, end: end ? end : videoSize - 1 });

//         console.log(start, end);
//     } catch (error) {
//         console.error(error);
//         ctx.status = 500;
//         ctx.body = 'Internal Server Error';
//     }
// });

// app.on('error', async (error, ctx) => {
//     if (error.code === "ECONNRESET") {
//         retryCount++;
//         console.log(`Reconnecting... Attempt ${retryCount}`);
//     }
// });

// app.listen(4000);
