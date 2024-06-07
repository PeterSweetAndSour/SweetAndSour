// From https://dev.to/devland/build-a-real-time-chat-app-using-nodejs-and-websocket-441g

import { createServer } from 'http';
import staticHandler from 'serve-handler';
import ws, { WebSocketServer } from 'ws';

//serve static folder
const server = createServer((req, res) => {   // (1)
    return staticHandler(req, res, { public: 'public' });
});

const wss = new WebSocketServer({ server }) // (2)
let colors = {};
let conversation = [];

wss.on('connection', (client) => {
		console.log('Client connected !');
		if (client.readyState === ws.OPEN) {
			client.send(JSON.stringify(conversation));
		}

    client.on('message', (dataStr) => {    // (3)
        let data = JSON.parse(dataStr);

        let messageTxt = data.messageTxt;
        let firstName = data.firstName;
        let bgColor = data.bgColor;
				let timestamp = data.timestamp;
        colors[firstName] = bgColor;

        console.log(`messageTxt: ${messageTxt}, firstName: ${firstName}, bgColor: ${bgColor}, timestamp: ${timestamp}`);

        let keys = Object.keys(colors);
        keys.forEach((key) => {
            console.log(`${key}: ${colors[key]}`);
        });
        
				let Json = {"messageTxt": messageTxt, "firstName": firstName, "bgColor": bgColor, "colors": colors, "timestamp": timestamp};
				conversation.push(Json);
        broadcast("[" + JSON.stringify(Json) + "]");
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
 