var canvas = document.getElementById("c");
var canvasContext = canvas.getContext("2d");
var g = Math.cos;
var i = Math.sin;

canvas.width=innerWidth;
canvas.height=innerHeight;

var j = canvas.width/2;
var k = canvas.height/2;
var l=[];
var m = 0;
var n = 0;

canvas.onmousemove=function(evt){
	m = d.clientX;
	n = d.clientY
};

(
	// Click to update the formula:
	// Math.PI*Math.sin(Math.sqrt(x*x+y*y))/Math.sqrt(x*x+y*y)
	canvas.onclick=function(d){
		l=[];
		d = new Function("x,y","return "+(d ? prompt("function", "Math.PI*Math.sin(Math.sqrt(x*x+y*y))/Math.sqrt(x*x+y*y)") : "Math.PI*Math.sin(Math.sqrt(x*x+y*y))/Math.sqrt(x*x+y*y)"));
		
		for(var f=-10; f<10; f+=0.5) {
			for(var a=-10; a<10; a+=0.5) {
				l.push(
					{
						x: f * 10, 
						y: a * 10, 
						a: 20 * d(f, a)
					}
				)
			}
		}
	}
)();

setInterval(
		function(){
			var d = 1.0E-4 * (m - j);
			var f = Math.cos(d);
			
			d = Math.sin(d);
			
			var a = 1.0E-4 * (n - k);
			var o = Math.cos(a);
			var p = Math.sin(a),b,h;
			
			for(a=l.length; a--;){
				b = l[a];
				h = b.a*f+b.x*d;
				b.x = b.x*f-b.a*d;
				b.a = h*o+b.y*p;
				b.y = b.y*o-h*p;
				h = 200/(200+b.a);
				b.b = j+b.x*h;
				b.canvas = k + b.y * h
			}
			
			canvasContext.clearRect(0,0, canvas.width, canvas.height);
			if(l.length){
				canvasContext.strokeStyle = "rgb(40,50,20)";
				canvasContext.beginPath();
				canvasContext.moveTo(l[0].b, l[0].canvas);
				f=l.length;
				for(a=1; a<f; a++) {
					try{
						a%40 ? canvasContext.lineTo(l[a].b,l[a].canvas) : canvasContext.moveTo(l[a].b, l[a].canvas)
					}
					catch(q){
					}
					
					canvasContext.stroke()
				}
			}

			canvasContext.fillText("Click to edit equation",99,99)
		}, 
		0
	); 