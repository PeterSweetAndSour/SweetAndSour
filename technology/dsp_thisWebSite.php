<? 
//dsp_thisWebSite.php
?>
<h2>2020 Site overhaul</h2>
<div class="story">
	<p>Since the last major redesign in 2008 two major things have happened:</p>
	<ol>
		<li>People using the mobile phones to view the internet has become a thing</li>
		<li>We started looking for a house in 2008 and bought a wreck in late 2009. Even after the builder finally &ldquot;finished&rdquo; in July 2011, it seemed that all my free time was now taken by home and garden improvement and this site was not updated to deal with #1.</li>
	</ol>
	<p>Clicking on a thumbnail image on the old version of this site popped up a fake window to show the full size version. It was workable on a desktop. Most of the time. To make the site responsive I wanted a modern image gallery/slider but I could not find one that supported long captions. My thumbnails often have a few words of 'click bait" and then the main caption is displayed with the large photo which seems a great approach to me but apparently no one else. I liked <a href="https://www.photoswipe.com" target="_blank" class="external">Photoswipe</a> because it lazy-loads the photos and you can set it up to work with your page structure so I ended up modifying it which took quite some time. <a href="https://sweetandsour.org/photoswipe_demo.html">Demo page</a>.</p>
	<p>I'll also be revisiting all the styles now that I'm using SASS and Grunt and browsers are now more capable.</p>
	<p>One thing I am pleased with is that I was able to embed the SVG of the four Font Awesome icons I used directly into the stylesheet. I wanted a background image rather than insert them with italic tags to avoid problems with clicks being on the wrong element and I didn't want to download the whole file of 1500+ icons, or make 4 separate requests for the indivual files, or even one request to a sprite that I'd have to make so embedding the SVG worked well.</p>
	<p>The 21 May 1999 note at the bottom of the home page that says &ldquo;<em>Since 18 Aprl</em> [when the site first went online]<em>, I've also bought a 56k modem and Homestead released an off-line editor. Previously, everything had to be done while logged on with my 14.4k modem which tied up the phone line for hours.</em>&rdquo; How times have changed! Typical download speeds are now more than a thousand times faster than when I started and that is why the photos on the early sections of the site are so small. I was still on dial-up until we moved to Virginia in 2006. There is also mention of building the site with &ldquo;web safe colors&rdquo; &ndash; all 256 of them. :-(</p>
	<p>The last major change I wanted to make was to move to friendly URLs so, for example, <code>/technology/thiswebsite</code> instead of <code>/technology/index.php?fuseAction=thisWebSite</code>. However, almost all of the site&apos;s internal URLs were relative so I need to change to &ldquo;root relative&rdquo; which turned out to be a problem when <code>/technology</code>, <code>/technology/</code>, <code>/technology/thiswebsite</code> and <code>/technology/thiswebsite</code> should all go to the same place.</p>
	<p>Worse, on my development computer running Apache, URLs are <code>localhost:8080/sweetandsour/&hellip;</code>. I eventually made all the code substitutions (global Search &amp; Replace - including in the exported version of the database which I will shortly reimport - initially gave me some surprises but what baffled me for a long time was that setting rewrite rules in .htaccess had no effect. Eventually I realized that the .htaccess file in my sweetandsour directory needed to be moved up a level to where DOCUMENT_ROOT actually is, not the base directory of sweetandsour.</p>
	<p>My local .htaccess file ended up as this:</p>
	<pre>
	RewriteEngine on

	# Links in the database are now hard coded to be root relative for live so need adjusting for local.
	RewriteCond %{HTTP_HOST}" "localhost:8080"
	RewriteRule ^(imagemgt|artsandculture|technology|whoweare)/(.*)$ /sweetandsour/$1/$2 [PT]

	# Human friendly URLs need to be translated internally so the server knows how to handle requests.
	RewriteCond %{HTTP_HOST}" "localhost:8080"
	RewriteRule ^sweetandsour/([a-z]+)/([a-zA-Z0-9-]+)/?$ /sweetandsour/$1/index.php?fuseAction=$2 [R]
	</pre>
	<p>I was surprised that you can't have multiple instances of RewriteRule following a single RewriteCond. In theory on Apache 2.4+ this should work:</p>
	<pre>
	&lt;If "%{HTTP_HOST} == 'localhost:8080'"&gt;
	RewriteRule ^(imagemgt|artsandculture|technology|whoweare)/(.*)$ /sweetandsour/$1/$2 [PT]
	RewriteRule ^sweetandsour/([a-z]+)/([a-zA-Z0-9-]+)/?$ /sweetandsour/$1/index.php?fuseAction=$2 [R]
	&lt;/If&gt;
	</pre>
	<p>but in practice it just gave me server errors. If you can explain why, do let me know.</p>

	<p>Everything below is probably no longer relevant but is retained for historical purposes&hellip;</p>
	<hr>
</div>


<h2>2009 Amazon S3 as a CDN</h2>
<div class="story">
	<p>[July 2009] Moving images and PDF files from my own server to a content delivery network (CDN) has the potential to speed up page loading since their huge number of distributed servers should deliver content more quickly than mine and if there is a huge surge in traffic, my server can concentrate on processing the PHP pages. Since I also intend to later set the &quot;expires&quot;&quot; to none, I've also renamed every file to carry the date it was modified so that if a new version is uploaded, it will have a new URL</p>
	<p>This is most of what you need to know about S3:</p>
	<ul>
		<li><a href="http://developer.amazonwebservices.com/connect/entry.jspa?externalID=1073&categoryID=55" title="Media hosting with S3" target="_blank" class="external">Scalable Media Hosting with Amazon S3</a></li>
		<li><a href="http://developer.amazonwebservices.com/connect/entry.jspa?externalID=1109&categoryID=55#08" title="S3 file system" target="_blank" class="external">How do I mimic a typical file system on Amazon S3?</a></li>
	</ul>
	<p>In particular, the <a href="https://addons.mozilla.org/en-US/firefox/addon/3247" title="S3Fox" target="_blank" class="external">S3Fox add-on</a> makes it relatively painless as it creates &ldquo;keys&rdquo; that <em>look like</em> a file path and name even though S3 essentially gives you one big &ldquo;bucket&rdquo; and everything gets tossed in there. </p>
	<p>One trap I fell into is that if you click the S3Fox in the browser status bar, you do not get the same view as selecting it from the Tools menu. Once you get that sorted out, moving files functions much like an FTP client.</p>
	<p>I read another article, <a href="http://www.labnol.org/internet/setup-content-delivery-network-with-amazon-s3-cloudfront/5446/" title="S3 as CDN" target="_blank" class="external">How to Setup Amazon S3 with CloudFront as a Content Delivery Network</a>, that used CloudFront as well but I'm not clear on why I would want that, especially as it appears you get charged separately for the traffic through there. I didn't need it though.</p>
	<p>Note that if you are running YSlow in Firebug, it does not know that sweetandsour.org.s3.amazonaws.com is a CDN so it unfairly assigns an &ldquo;F&rdquo; grade.</p>
	<p>I struggled with S3Fox to set headers and eventually gave up. I never could figure out the syntax it wanted and couldn't find any on the internet. Instead, I found <a href="http://www.cloudberrylab.com/" target="_blank" class="external">Cloudberry Explorer</a> and have now set an Expires header for the end of the year.</p>
	<p>One limitation of S3 is that it isn't a web server able to negotiate with the user agent on whether to send compressed files. I moved the CSS and JS back to my server where I can run mod_deflate.</p>
	<!--
	<h4>Results</h4>
	<p>Loading the page with pictures of my <a href="http://www.sweetandsour.org/photos/index.php?fuseAction=aust2007">2007 trip back home</a> with a cleared cache three times took 9.5, 7.9 and 8.2 seconds with the old system. 
	-->
</div>

<h2>2008 Site redesign</h2>
<div class="story">
	<p>[October 2008] I've thought for years that the site looked really crude and I've finally done something about it. Here are the main features and changes:</p>
	<ul>
		<li>New navigation based on the <a href="http://phatfusion.net/imagemenu/" target="_blank" class="external">Phatfusion &quot;Image Menu&quot;</a> but extended so it will handle 2nd and 3rd level menu items. In fact, I had to make some changes to even get the top-level menu to work as Mootools 1.2 has been released since the menu was written and there were some Javascript errors.</li>
		<li>In fact, I suggest that my version of the &quot;image menu&quot; is better than the original since I've used the <a href="http://www.mezzoblue.com/tests/revised-image-replacement/#gilderlevin" target="_blank" class="external">Gilder/Levin image replacement method</a> which means it is still usable even if images are turned off. Try that on Phatfusion.net.</li>
		<li>Since I didn't take all the photos used in the &quot;image menu&quot;, I needed to contact the copyright holders and ask for their permission where necessary. I used <a href="http://powazek.com/posts/867" class="external" target="_blank">A Savvy Approach to Copyright Messaging</a> so that if anyone tries to download the graphic from the menu above, they will see this as part of the larger menu sprite:
				<img src="http://sweetandsour.org/images/menuComponents/technology.jpg" alt="Example of displaying copyright unobtrusively" /></li>
		<li>New popup image holder. The &quot;image menu&quot; requires <a href="http://mootools.net/" target="_blank" class="external">Mootools</a> and that created a conflict with the old DHTML window that photos were loaded into when the thumbnail was clicked. I was delighted that I replaced the old dhtmlWindow.js file of 987 lines with perhaps one-third of that, now in common.js. </li>
		<li>Rather than attempt to explain it, you will want to do a View &gt; Source on this page to see one particular feature.</li>
		<li>One of the few (only?) slightly worthwhile things I've done in my life is to learn about the Israeli-Palestinian conflict and write to some public figures begging that the Palestinian grievances be addressed so that there can be a lasting peace in the Middle East. I pulled this out into a new section &quot;Time for Justice&quot;. I encourage you to get informed and get involved.</li>
		<li>Since they are everywhere else on the internet, I felt compelled to throw in some rounded corners. Since Firefox and Safari support them, there are some lines in the stylesheet but for IE, some &lt;span&gt; tags are inserted by Javascript and they have background images. Yes, that means that no JS and no rounded corners. As the philosopher <a href="http://en.wikipedia.org/wiki/Mick_Jagger" class="external" target="_blank">Jagger</a> has noted, &quot;You can't always get what you want&quot;.</li>
		<li>Since it is offered for free, I thought I'd try <a href="http://code.google.com/apis/ajaxsearch/" target="_blank" class="external">Google AJAX Search</a>. However, I set it up to show the results on the original page, similar to how Mootools have done it in their documentation section.</a>
		<li>The &quot;countdown&quot; site introduction animation and highlight of my Java programming has been retired. It seemed such a good idea in 1999. However, if you are desperate to see it, this should satisfy: <a href="countdown.html" target="_blank">Countdown</a>. The other thing I did in Java was <a href="dsp_minesweeper.htm" target="_blank">minesweeper</a>.</li>
	</ul>
</div>

<h2>I wrote the following in about 2003. </h2>
<div class="story">
   <p>This latest revision to the site uses XHTML/Transitional and CSS to achieve a clearly-structured document and layout without tables so that content is separated from presentation. I'll shortly be finalizing the code so it will pass the W3's validator and move towards Section 508 compliance which ensures accessibility to those with disabilities (such as blind people using screen readers). The benefits of making the site &quot;standards-compliant&quot; are faster-loading pages, easier maintenance and availability to users with a variety of browsers.</p>

   <p>As you can see from the first entries in the &quot;What&rsquo;s new&quot; section, this web site was started in 1999 on Homestead.com using their WYSIWYG editor. Although it was simple to use, it produced static HTML files that were bulky and nearly incomprehensible to a human reader.  The site became very difficult to maintain since any change to the menu had to be replicated on every page.  Homestead.com realized they couldn't make money if they didn't charge for the service so when they introduced substantial fees, the site was moved to GeoCities.com.  This forced me to rewrite and simplify the pages but they were still static and maintenance was problematic.</p>
   <p>I had always wanted to rebuild the site with dynamic pages so the next stage implemented in 2003 was to rebuild the site using  &quot;LAMP&quot; which stands for <b>Linux</b> (operating system)/<b>PHP</b> (scripting language)/<b>Apache</b> (web server)/<b>mySQL</b> (database).  All are effectively free.  In fact, my computer is set up with both Windows and Linux partitions with a shared data partition in VFAT format so I can test with <b>Internet Explorer</b> and <b>Opera</b> and <b>Firefox</b> browsers under Windows as well as <b>Konqueror</b> and <b>Firefox</b> under Linux.</p>
   <p>The site also uses the "fusebox" methodology, a system originally invented in the ColdFusion world.  All page requests end up at an "index" file which is a series of case statements and the &quot;fuse action&quot; determines which files are then included to form the completed page.  While the mechanics are not important, what is important is that it forces a modular approach where pages are broken down and hopefully the pieces are reusable elsewhere.</p>
   <p>To enforce a consistant look, there are two types of display pages.  All the pages which show the menu use &quot;dsp_outline.php&quot; which sets up a the overall page framework and includes the photo at the top-left, the headings, the menu and the &quot;content page&quot;.  The &quot;content page&quot; is specified in the index page for that directory, while the colors are specified in an &quot;app_locals&quot; file which is also directory-specific.  Each &quot;app_locals&quot; file includes &quot;app_globals&quot; that sets a few variables that affect the entire site such as database log in details.</p>
   <p>Photographs are placed using the database which holds the name, caption, folder and any linked image for every photograph.  The file act_setThumbnailFunction.php defines a function called once for each thumbnail that writes out the HTML for the floating DIV, the image, link URL and caption.  If necessary, the link URL can be overridden.  The large image is shown using dsp_photoAndCaption.php which makes a call to the database to find the caption and places the caption to the side or below the image depending on its size.</p>
</div>
<div class="photo-gallery">
	<? setThumbnail(["PeterAtWork1.jpg", "MoirePattern1.jpg", "WebSafeColors1.gif"]); ?>
</div>




