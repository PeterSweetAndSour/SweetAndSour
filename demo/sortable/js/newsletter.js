/*
 * functionality shared across newsletters
 */
var newsletter = {
	initialize : function() {
		try {
			// exit if document is a popup
			if (Element.hasClassName(document.body, "popup")) return;

			Fool.setLayout(); // setup screen
			Event.observe(window, 'resize', Fool.setLayout); // listen for layout events

			this.search.prepare();	// manipulate search fields / button
		} catch (err) {
			//fail silently
			//alert(err)
		}
	},
	search : {
		prepare : function () {
			var inputs = document.getElementById("search").getElementsByTagName("input");
			this.input = inputs[0];
			this.btn = inputs[1];
			this.postBack = this.btn.onclick;
			
			this.input.onkeydown = this.submitOnReturn.bindAsEventListener(this);
			this.btn.onclick = this.submit.bindAsEventListener(this);
		},
		submitOnReturn : function (event) {
			if (event.keyCode == 13) {
				this.submit();
			}
		},
		submit : function (event) {
			if (this.input.value.trim()) {
				this.postBack();
			} else {
				Event.stop(event);
			}
		}
	},
	handleSnapshotLinks : function() {
		var links = $$("a.snapshot");
		var features = "menubar=no,location=yes,resizable=yes,scrollbars=yes,personalbar=no,status=no,width=550,height=350";
		
		// avoid ie memory leaks drips
		function click (href) {
			var PID = /\/\d{2}\//.exec(href);
			var args = href.match(/\?([^$])+/)[0] + "&returnUrl=" + document.URL;
			
			var snapshotUrl = PID + "scorecard/ServiceSnapShotPop.aspx" + args;	
			var w = window.open(snapshotUrl, (new Date()).getTime(), features);
			if (w) {
				w.opener = window;
				// block click event if popup was successfull
				return false;
			}
			return true;
		}
		
		function setup (link, i) {
			link.onclick = function() {
				return click(this.href);
			}
			link = null;
		}
		links.each(setup);
	},
	highlight : function (searchTerm, element, className) {
		var highlightStartTag = "<span style=\"background-color:yellow;\">";
		var highlightEndTag = "</span>";
	  
	  // find all occurences of the search term in the given text,
		var newText = "";
	  var i = -1;
		element = element || document.body;
		var bodyText = element.innerHTML;
	  var lcSearchTerm = searchTerm.toLowerCase();
		var lcBodyText = element.innerHTML.toLowerCase();
	    
	  while (bodyText.length > 0) {
	    i = lcBodyText.indexOf(lcSearchTerm, i+1);
	    if (i < 0) {
	      newText += bodyText;
	      bodyText = "";
	    } else {
	      // skip anything inside an HTML tag
	      if (bodyText.lastIndexOf(">", i) >= bodyText.lastIndexOf("<", i)) {
	        // skip anything inside a <script> block
	        if (lcBodyText.lastIndexOf("/script>", i) >= lcBodyText.lastIndexOf("<script", i)) {
	          newText += bodyText.substring(0, i) + highlightStartTag + bodyText.substr(i, searchTerm.length) + highlightEndTag;
	          bodyText = bodyText.substr(i + searchTerm.length);
	          lcBodyText = bodyText.toLowerCase();
	          i = -1;
	        }
	      }
	    }
	  }
	  element.innerHTML = newText;
	},
	openLinksInParent : function() {
		var links = document.getElementsByTagName("a");
		$A(links).each(
			function(link,i){
				link.onclick = function() {
					if (window.opener) {
						window.opener.document.location.href = this.href;
						window.close();
						return false;
					} else {
						return true;
					}
				};
			})
	},
	injectInlineIcon : function (target, title) {
		var span = document.createElement("span");
		span.className = "inlineIcon";
		span.title = title || "";

		$$(target).each(function (div, i) {
			// assume we want to inject icon into first P element found
			var p = div.getElementsByTagName("p")[0];
			if (p) p.insertBefore(span, p.firstChild);
		});
	}
}

Event.onDOMReady(function() {
	newsletter.initialize();
	Fool.Widgets.Ratings.init();
	Fool.striper("table#scorecardTable");

	newsletter.injectInlineIcon("div.advisorBio", "Mathew Emmert");
	newsletter.injectInlineIcon("div.newMoneyNow", "New Money Now");
	
	// TODO: need better object branch for IE
	Fool.PseudoClass.hover("#siteNav li");
	if (document.all) {
		Fool.PseudoClass.hover("input.button");
		Fool.PseudoClass.hover("input.text");
	}
});
