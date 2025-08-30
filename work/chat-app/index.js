/* From https://dev.to/devland/build-a-real-time-chat-app-using-nodejs-and-websocket-441g */
import { createServer } from 'http';
import staticHandler from 'serve-handler';
import ws, { WebSocketServer } from 'ws';

//serve static folder
const server = createServer((req, res) => {   // (1)
    return staticHandler(req, res, { public: 'public' });
});

const wss = new WebSocketServer({ server }) // (2)
let colors = {};

wss.on('connection', (client) => {
    console.log('Client connected !');

    client.on('message', (dataStr) => {    // (3)
        let data = JSON.parse(dataStr);

        let messageTxt = data.messageTxt;
        let firstName = data.firstName;
        let bgColor = data.bgColor;
        colors[firstName] = bgColor;

        console.log(`messageTxt: ${messageTxt}`);
        console.log(`firstName: ${firstName}`);
        console.log(`bgColor: ${bgColor}`);

        let keys = Object.keys(colors);
        keys.forEach((key) => {
            console.log(`${key}: ${colors[key]}`);
        });
        
        broadcast(JSON.stringify({"messageTxt": messageTxt, "firstName": firstName, "bgColor": bgColor, "colors": colors}));
    })
})
function broadcast(msg) {       // (4)
    for (const client of wss.clients) {
        if (client.readyState === ws.OPEN) {
            client.send(msg);
        }
    }
}
server.listen(process.argv[2] || 8080, () => {
    console.log(`server listening...`);
})
