import java.awt.*;
import java.lang.Math;
import java.net.URL;
import java.net.MalformedURLException;

public class Countdown extends java.applet.Applet implements Runnable {

//------------------------------------------------------------------------
// Declarations
//------------------------------------------------------------------------
  int cycles     = 0;
  int maxCycles  = 250; 
  int countDown = 6; 
  String message = "";

  Font f1 = new Font("Helvetica", Font.BOLD, 36);
  FontMetrics fmetrics = getFontMetrics(f1);
  Font f2 = new Font("Helvetica", Font.BOLD, 150);

  int x = 250;                     // Center of orbit, x value
  int y = 170;                     // Center of orbit, y value
  int xOffset = 0;                 // To position orbiting circle
  int yOffset = 0;                 // To position orbiting circle
  double XOffset;                  // Used to calculate int xOffset
  double YOffset;                  // Used to calculate int yOffset

  double rad = 100.0;              //Radius of orbit
  double angle = (-Math.PI)/2;     //Angle in radians from start at "12 o'clock"


//  I did consider putting these in an array but it hardly seemed worth it for
//  seven colors plus naming them makes it explicit what they are.

  Color circleColor = new Color(255, 102, 102); // Red. For big circle's color
  Color orange5     = new Color(255, 153, 051);
  Color yellow4     = new Color(255, 255, 0);
  Color green3      = new Color(051, 204, 153);
  Color cyan2       = new Color(0, 255, 255);
  Color blue1       = new Color(051, 153, 255);
  Color violet0     = new Color(204, 153, 255);

  Thread runOrbit;

  Image buffer;
  Graphics g2;                     // For double buffering

  // Create URL for applet to link to when it is finished
  URL ourURL;
  String url = "../lanpeter/index.html";


//------------------------------------------------------------------------
// Methods
//------------------------------------------------------------------------
  public void init() {
     // Handle the new URL
     try { ourURL = new URL(url); }
     catch (MalformedURLException e) {
        System.out.println("URL not found: " + ourURL);
     }

     // Two new instances created below for double-buffering
     buffer = createImage(size().width, size().height);
     g2 = buffer.getGraphics();
  }

  public void start() {
     if (runOrbit == null) {
        runOrbit = new Thread(this);
        runOrbit.start();
     }
  }

  public void stop() {
     if (runOrbit != null) {
        runOrbit.stop();
        runOrbit = null;
     }
  }

  public void run() {
     while (countDown > 0)  {
        switch (countDown) {
           case 6:
              break;
           case 5:
              cycles = 0;
              angle = (-Math.PI)/2;
              circleColor = orange5;
              message = "Preparing to enter";
              break;
           case 4:
              cycles = 0;
              angle = (-Math.PI)/2;
              circleColor = yellow4;
              message = "Lan & Peter's web site.";
              break;
           case 3:
              cycles = 0;
              angle = (-Math.PI)/2;
              circleColor = green3;
              message = "Oh ... by the way, ";
              break;
           case 2:
              cycles = 0;
              angle = (-Math.PI)/2;
              circleColor = cyan2;
              message = "if you are an employer,";
              break;
           default:
              cycles = 0;
              angle = (-Math.PI)/2;
              circleColor = blue1;
              message = "Peter needs a job.";
              break;
        }

        while (cycles < 50) {
           cycles++;
           angle = angle +  0.1257;   // 50 cycles for one rotation

           // Calculate offset from center of orbit - gives a double
           XOffset = rad * Math.cos(angle);
           YOffset = rad * Math.sin(angle);

           // Convert to int
           xOffset = (int)(XOffset);
           yOffset = (int)(YOffset);

           // Slow down loop for easy viewing
           try {Thread.sleep(20);}
           catch (InterruptedException e) {}

           repaint();
        }
        countDown--;
     }
  circleColor = violet0;
  repaint();
  getAppletContext().showDocument(ourURL);        // Transfer to our main home page
  }

  public void paint(Graphics g) {
     setBackground(Color.white);
     g2.clearRect(0, 0,size().width, size().height);
     g2.setColor(Color.black);

     g2.setFont(f1);
     g2.drawString("" + message, (size().width - (fmetrics.stringWidth(message)))/2, 40); // Show message in center

     g2.fillOval(x - 100, y - 100, 200, 200);       // Draw black background cicle
                                                    // (heavy border for colored cicle)
     g2.setColor(circleColor);                 // Draw colored circle
     g2.fillOval(x - 95, y - 95, 190, 190);
     g2.setColor(Color.black);
     g2.drawLine(x-100, y, x+100, y);               // Draw horizontal "cross-hair"
     g2.drawLine(x, y-100, x, y+100);               // Draw vertical "cross-hair"
     g2.drawLine(x, y, x + xOffset, y + yOffset);   // Draw line to sweep colored circle

     g2.setColor(Color.red);
     g2.fillOval( x + xOffset - 10, y + yOffset -10, 20, 20);
     g2.setColor(Color.black);
     g2.drawOval( x + xOffset - 10, y + yOffset -10, 20, 20);
     g2.setFont(f2);
     g2.drawString("" + countDown, x - 43, y + 55);

     g.drawImage(buffer, 0, 0, this);
  }

  public void update(Graphics g) {
     paint(g);
  }

}