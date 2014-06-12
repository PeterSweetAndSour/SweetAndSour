/*
Will allow "bounce angle" to be set by prompt but set to 140° for the moment.
Since we start at the point to the right of the circle's center we set off 20°
counterclockwise from vertical.
*/
var DegPerRad = 360/(2*Math.PI);
var bounceAngle = 67 / DegPerRad;           // 0° < bounceAngle < 180° then convert to radians

var startHeading = Math.PI - bounceAngle/2; // 180° minus half the bounce angle i.e. 110° counterclockwise from 3 o'clock
var hue = 0;                                // red
var intervalTime = 500;                     // milliseconds

var canvas = document.getElementById("c");
var ctx = canvas.getContext("2d");

canvas.width = innerWidth;
canvas.height = innerHeight;

var ctrX = canvas.width/2;
var ctrY = canvas.height/2;

ctx.fillStyle = "rgb(0,0,0)";  
ctx.fillRect (0, 0, canvas.width, canvas.height); 

drawText();

// Draw a circle that fills 80% min of height or width
var r = canvas.width > canvas.height ? Math.round(canvas.height * 0.4) : Math.round(canvas.width * 0.4);
ctx.moveTo(ctrX + r, ctrY);
ctx.beginPath(); 
ctx.arc(ctrX, ctrY, r, 0, 2*Math.PI, true);
ctx.strokeStyle = "rgb(255, 255, 255)";
ctx.stroke();

// Get segment length
segmentLength = 2 * r * Math.cos(bounceAngle / 2);

var startX = ctrX + r;
var startY = ctrY;

// Create an array of lines and push the first one in
var lines = [];
//console.log("pushing to lines, x: " + Math.round(startX*100)/100 + ", y: " + Math.round(startY*100)/100 + ", heading: " + Math.round(startHeading*DegPerRad*100)/100);
lines.push({x: startX, y: startY, heading: startHeading, hue: "hsla(" + hue + ", 100%, 50%, 1)"});

window.setInterval(
	function() {
		getNextPointAndHeading();
		drawLines();
	}, 
	intervalTime
)

canvas.onmousemove=function(evt){
	hue = evt.clientX / canvas.width * 255;
	//n = evt.clientY
};

function getNextPointAndHeading() {
	var lastEntry = lines[lines.length - 1];
	var lastHeading = lastEntry.heading;
	var deltaX = Math.cos(lastHeading) * segmentLength;
	var deltaY = Math.sin(lastHeading) * segmentLength;

	var nextX = lastEntry.x + Math.cos(lastHeading) * segmentLength;
	var nextY = lastEntry.y + Math.sin(lastHeading) * segmentLength;
	
	var nextHeading = (lastEntry.heading + (Math.PI - bounceAngle));
	if(nextHeading > (2*Math.PI)) {
		nextHeading = nextHeading - 2*Math.PI;
	}
	//console.log("deltaX =  r * " + Math.cos(lastHeading) + " = " + deltaX+ ", deltaY =  r * " + Math.sin(lastHeading) + " = " + deltaY + ", lastLine.heading: " + lastHeading * DegPerRad);
		
	lines.push({x: nextX, y: nextY, heading: nextHeading, hue: "hsla(" + hue + ", 100%, 50%, 1)"});
	if(lines.length > 360) {
		lines.shift();
	}
}

function drawLines() {
	for(var i=1; i < lines.length; i++) {
		ctx.beginPath(); 
		ctx.moveTo(lines[i-1].x, lines[i-1].y);
		ctx.lineTo(lines[i].x, lines[i].y);
		ctx.strokeStyle = lines[i].hue;
		ctx.stroke();
	}
}

function drawText() {
		ctx.font         = 'italic 30px arial';
		ctx.textBaseline = 'top';
		ctx.fillText  ('Hello world!', 10, 10);
}




