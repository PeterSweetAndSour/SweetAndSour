/*
 * Env (Environment)
 *================================================================================================*/
var Env = {
	parameter : function (key) {
		var vals = new RegExp(key + '=([^&]+)').exec(location.search);
		return (vals) ? decodeURIComponent(vals[1]) : undefined;
	}
};


/*
 * Position extensions
 *================================================================================================*/
Object.extend(Position, {
  pageScrollTop : function(){
  	var yScrolltop;
  	if (self.pageYOffset) {
  		yScrolltop = self.pageYOffset;
  	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
  		yScrolltop = document.documentElement.scrollTop;
  	} else if (document.body) {// all other Explorers
  		yScrolltop = document.body.scrollTop;
  	}
  	return yScrolltop;
  },
  viewportSize : function(){
  	var de = document.documentElement;
  	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
  	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
  	return {width:w, height:h};		
  },
	pageDimensions : function () {
		// TODO: cover quirksmode
		var de = document.documentElement;
		return {width:(de.scrollWidth > de.clientWidth) ? de.scrollWidth : de.clientWidth,
						height:(de.scrollHeight > de.clientHeight) ? de.scrollHeight : de.clientHeight}	
  }	
});


/*
 * String extensions
 *================================================================================================*/
Object.extend(String.prototype, {
	trim : function () {
		return (this == "") ? this : this.ltrim().rtrim();
	},
	ltrim : function () {
		return (this == "") ? this : this.replace(/^\s+/gm, '');
	},
	rtrim : function () {
		return (this == "") ? this : this.replace(/\s+$/gm, '');
	}
});


/*
 * Element extensions
 *================================================================================================*/
Object.extend(Element, {
	create : function(tagName, children, attributes) {
		var element = document.createElement(tagName);
		children = (children instanceof Array) ? children : [children];
		
		$H(attributes).each(function (v, i) {
			var key = v[0], value = v[1];
			switch (key.toLowerCase()) {
				case "classname" : 
					element.className = value;
					break;
				case "colspan" :
					element.colSpan = value;
					break;
				case "style" :
					element.style.cssText = value;
					break;
				default :
					element.setAttribute(key, value);
					break;
			}
		});
		
		$A(children).each(function (child, i) {
			// assume objects with nodeType property are dom
			element.appendChild((child.nodeType) ? child : document.createTextNode(child));
		});
		
		return $(element);
	}
});


/*
 * Element.prototype extensions
 *================================================================================================*/
Element.addMethods({
	// recursively remove empty text nodes and comments from DOM element
	collapse : function (element, isDeep) {
		var next, node = element.firstChild;
		while (node) {
			if (node.hasChildNodes() && (isDeep || isDeep == undefined)) {
				// recurse
				Element.collapse(node, isDeep);
			}
			next = node.nextSibling;
			if ((/3/.test(node.nodeType) && !/\S/.test(node.nodeValue)) || /8/.test(node.nodeType)) {
				node.parentNode.removeChild(node)
			}
			node = next;			
		}
	}
});
Object.extend(Element, Element.Methods);


/*
 * Event extensions
 *================================================================================================*/
Object.extend(Event, {
  _domReady : function() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;

    if (this._timer)  clearInterval(this._timer);
    
    this._readyCallbacks.each(function(f) { f() });
    this._readyCallbacks = null;
	},
  onDOMReady : function(f) {
    if (!this._readyCallbacks) {
      var domReady = this._domReady.bind(this);
      
      if (document.addEventListener)
        document.addEventListener("DOMContentLoaded", domReady, false);
        
        /*@cc_on @*/
        /*@if (@_win32)
            document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
            document.getElementById("__ie_onload").onreadystatechange = function() {
                if (this.readyState == "complete") domReady(); 
            };
        /*@end @*/
        
        if (/WebKit/i.test(navigator.userAgent)) { 
          this._timer = setInterval(function() {
            if (/loaded|complete/.test(document.readyState)) domReady(); 
          }, 10);
        }
        
        Event.observe(window, 'load', domReady);
        Event._readyCallbacks =  [];
    }
    Event._readyCallbacks.push(f);
  }
});


/*
 * Add-on to Prototype 1.5 speeds up $$ - v1 (2006/06/25)
 * http://www.sylvainzimmer.com/index.php/archives/2006/06/25/speeding-up-prototypes-selector/
 *================================================================================================*/
var SelectorLiteAddon=Class.create();SelectorLiteAddon.prototype={initialize:function(stack){this.r=[];this.s=[];this.i=0;for(var i=stack.length-1;i>=0;i--){var s=["*","",[]];var t=stack[i];var cursor=t.length-1;do{var d=t.lastIndexOf("#");var p=t.lastIndexOf(".");cursor=Math.max(d,p);if(cursor==-1){s[0]=t.toUpperCase();}else if(d==-1||p==cursor){s[2].push(t.substring(p+1));}else if(!s[1]){s[1]=t.substring(d+1);}
t=t.substring(0,cursor);}while(cursor>0);this.s[i]=s;}
},get:function(root){this.explore(root||document,this.i==(this.s.length-1));return this.r;},explore:function(elt,leaf){var s=this.s[this.i];var r=[];if(s[1]){e=$(s[1]);if(e&&(s[0]=="*"||e.tagName==s[0])&&e.childOf(elt)){r=[e];}
}else{r=$A(elt.getElementsByTagName(s[0]));}
if(s[2].length==1){r=r.findAll(function(o){if(o.className.indexOf(" ")==-1){return o.className==s[2][0];}else{return o.className.split(/\s+/).include(s[2][0]);}
});}else if(s[2].length>0){r=r.findAll(function(o){if(o.className.indexOf(" ")==-1){return false;}else{var q=o.className.split(/\s+/);return s[2].all(function(c){return q.include(c);});}
});}
if(leaf){this.r=this.r.concat(r);}else{++this.i;r.each(function(o){this.explore(o,this.i==(this.s.length-1));}.bind(this));}
}
}
var $$old=$$;var $$=function(a,b){if(b||a.indexOf("[")>=0)return $$old.apply(this,arguments);return new SelectorLiteAddon(a.split(/\s+/)).get();}


/*
 * Fool
 *================================================================================================*/
var Fool = {
	/*
	 * hiliteColumn
	 */
	hiliteColumn : function (table, idxOn, idxOff) {
		table = $(table);
		var trs = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr"), className = "sortColumn";
		
		for (var i = 0; i < trs.length; i++) {
			var tr = trs[i];
			if (idxOff != undefined) {
				Element.removeClassName(tr.childNodes[idxOff], className);
			}
			Element.addClassName(tr.childNodes[idxOn], className);
		}
	},
	/*
	 * applySortableTables - attaches events to TH elements to sort a table based on user action
	 */
	applySortableTables : function () {
	  try {
	    var consts = {CLASS_HEADER  : "sortHeader",
	                  CLASS_ASCEND  : "sortAscend",
	                  CLASS_DESCEND : "sortDescend"};

			// declare mousedown out here to limit scope
			function mousedown (th, i) {
				var tr = th.parentNode;
				var table = tr.parentNode.parentNode;

				var sorter = Fool.Sortable.sorters[table.sorterId];
				sorter.options.idx = i;
				if (sorter.cache[i]) sorter.items = sorter.cache[i];
				else sorter.cache[i] = sorter.getItems();

				Fool.hiliteColumn(table, i, table.activeIdx);

				var activeTH = tr.childNodes[table.activeIdx];
				table.activeIdx = i;

				if (activeTH) {
					Element.removeClassName(activeTH, consts["CLASS_ASCEND"]);
					Element.removeClassName(activeTH, consts["CLASS_DESCEND"]); 
					if (activeTH == th) {
						//
						// reverse sort order within same column
						if (sorter.direction == "ASCEND") {
							Element.addClassName(th, consts["CLASS_DESCEND"]);
							sorter.reverse();						
						} else {
							Element.addClassName(th, consts["CLASS_ASCEND"]);
							sorter.sort();		
						}
					} else {
						//
						// select new column, maintain sort order
						if (sorter.direction == "ASCEND") {
							Element.addClassName(th, consts["CLASS_ASCEND"]);
							sorter.sort();						
						} else {
							Element.addClassName(th, consts["CLASS_DESCEND"]);
							sorter.reverse();		
						}
					}
				} else {
					//
					// first click
					Element.addClassName(th, consts["CLASS_ASCEND"]);
					sorter.sort();
				}
				if (/\bstriped\b/.test(table.className)) Fool.striper(table);

				// de-leak-a-fy
				th = tr = table = null;
			}

	    function setupTh (th, i) {
				if (Element.empty(th) || th.innerHTML == "&nbsp;") return;

				Element.addClassName(th, consts["CLASS_HEADER"]);
	      th.title = "Click to sort";
				th.onmousedown = function () {
					mousedown(this, i);
				}

				// de-leak-a-fy
				th = null;
	    }

	    function setupTable (table) {
				var sorter = new Fool.Sortable.Table(table);
				sorter.cache = {};
				sorter.cache[sorter.options.idx] = sorter.items;

				var len = Fool.Sortable.sorters.length;
				Fool.Sortable.sorters[len] = sorter;
				table.sorterId = len;

	      var ths = table.getElementsByTagName("THEAD")[0].getElementsByTagName("TH");
				$A(ths).each(setupTh);

				// de-leak-a-fy
				table = null;
	    }

	    $$("table.sortable").each(setupTable);
	  } catch (err) {
			throw err;
	  }
	},

	/*
	 * handleSortable - call to sort all .sortable elements
	 */
	handleSortables : function () {
		$$("dl.sortable").each(function(v, i){Fool.sortElement(v);});
		$$("ol.sortable").each(function(v, i){Fool.sortElement(v);});
		$$("ul.sortable").each(function(v, i){Fool.sortElement(v);});
		$$("table.sortable").each(function(v, i){Fool.sortElement(v);});
	},

	/*
	 * setLayout
	 */
	setLayout : function () {
		if (Element.getDimensions(document.body).width < 1000) {
			Element.addClassName(document.body, 'narrow');
		} else {
			Element.removeClassName(document.body, 'narrow');
		}
	},

	/*
	 * sortElement - called on an individual list or table elemnt
	 */
	sortElement : function (element, options) {
	  element = $(element);
	  var Sortable, ns = Fool.Sortable;
	  switch (element.nodeName) {
	    case "DL" :
	      Sortable = ns.DefinitionList; break;
	    case "OL" :
	    case "UL" :
	      Sortable = ns.GenericList; break;
	    case "TABLE" :
	      Sortable = ns.Table; break;
			default :
				return;
	  }

	  var instance = new Sortable(element, options);
	  instance.sort();
	},
	
	/*
	 * striper
	 */
	striper: function (selector) {
		var containers = ((selector.nodeType) ? [$(selector)] : $$(selector)), container;
		
		for (var i = 0; i < containers.length; i++) {
			container = containers[i];
			Element.addClassName(container, "striped");
			var rows = container.getElementsByTagName((container.tagName == 'TABLE') ? "tr" : "li");
			for (var n = 0; n < rows.length; n++) {
				Element.addClassName(rows[n], (rows[n].className = (n % 2) ? "even" : "odd"));
			}
		}
	}
};

/*
 * Fool.PseudoClass
 *================================================================================================*/
Fool.PseudoClass = {
	hover : function(selector) {
		var elements = $A( $$(selector) );
		elements.each(function(el) {
										if (el.innerHTML.trim() != "&nbsp;") {
											Event.observe(el, 'mouseover', function() {Element.addClassName(el, 'hover');});
											Event.observe(el, 'mouseout', function() {Element.removeClassName(el, 'hover');});
										}
									});
	}
};

/*
 * Fool.Cookies
 *================================================================================================*/
Fool.Cookies = {
	get : function(name) {
		var values = new RegExp(name + '=([^;]+)').exec(document.cookie);
		return (values == null) ? undefined : decodeURIComponent(values[1]);
	}
};

/*
 * Fool.Widgets
 *================================================================================================*/
Fool.Widgets = {}

/*
 * Fool.Widgets.Ratings
 *================================================================================================*/
Fool.Widgets.Ratings = {
	init: function() {
		// mseeley, 8/08/06 moved here to for easier init
		if (!$("ratings")) {
			return;
		} 
	
		$$('#ratings a').each( function(a) {
			Event.observe(a, 'click', function(e) {
				$('calculatingRatings').style.visibility = 'visible';
				Fool.Widgets.Ratings.submit(a.innerHTML);
				Event.stop(e);
			});
			Event.observe(a, 'mouseup', function() {
				a.blur();
			});
		});

		// get user's ratings
		new Ajax.Request (
			'/ajax/GetUserRating.aspx',
			{
				method: 'get',
				parameters: 'url=' + document.location.href,
				onComplete: function(xhr) {
					if (xhr.responseText != '0') {
						$('usrRatingHeading').innerHTML = 'Your rating:';
					}
					$('currentUserRating').style.width = xhr.responseText * 18 + 'px';
				}
			}
		); 

		// get current avg ratings
		new Ajax.Request (
			'/ajax/RateContent.aspx',
			{
				method: 'get',
				parameters: 'url=' + document.location.href,
				onComplete: function(xhr) {
					$('currentAvgRating').style.width = xhr.responseText * 18 + 'px';
				}
			}
		);
	},

	submit: function(score) {
		$('currentUserRating').style.width = score * 18 + 'px';
		// send ajax request
		new Ajax.Request (
			'/ajax/RateContent.aspx',
			{
				method: 'get',
				parameters: 'rating=' + score + '&url=' + document.location.href,
				onComplete: function(xhr) {
					$('usrRatingHeading').innerHTML = 'Your rating:';
					setTimeout( function() {
						$$('div#currentAvgRating span').each( function(span) {
							span.style.width = xhr.responseText * 18 + 'px';
							});
						$('calculatingRatings').style.visibility = 'hidden';
						}, 2000); // 2s delay - might need to tweak when we go live
				}
			}
		); 
	}
};

/*
 * Fool.Sortable
 *================================================================================================*/
Fool.Sortable = {
	interface : function (element, options) {
		element = $(element);
		element.collapse();
		
		this.element = element;
    this.options = options || {};
		this.items = $A();
    this.direction;
		
		function compare (a, b) {
			var a = a.text, b = b.text;
			if (typeof(a) == "number" && typeof(b) == "string") b = Number.NEGATIVE_INFINITY;
			else if (typeof(a) == "string" && typeof(b) == "number") a = Number.NEGATIVE_INFINITY;
			return (a > b) ? 1 : -1;
		}
		
		this.sort = function (options) {
			this.items.sort((options && options.compareFn) ? options.compareFn : compare);	
      this.direction = "ASCEND";
			this.render();
		};

		this.reverse = function () {
			this.sort();
			this.items = this.items.reverse();
      this.direction = "DESCEND";
			this.render();
		};
		
		this.getItems = function (nodeList) {
			var items = this.items, Item = Fool.util.sortable.Item;
			$A(nodeList).each(function(v,i){items[items.length] = new Item(v);});
			return this.items;
		};

		this.render = function () {
			var target = this.element, children = target.childNodes, idx = 0;
			function reorder (v, i) {target.insertBefore(v.element, children[idx++]);}
			this.items.each(reorder);
		};
	},

	sorters : [],

	/*
	* Fool.Sortable.GenericList  - UL & OL
	*===============================================================================================*/
	GenericList : function (element, options) {
		Fool.Sortable.interface.apply(this, arguments);

    this.getItems(element.getElementsByTagName("LI"));
	},


	/*
	* Fool.Sortable.DefinitionList  - DL
	*===============================================================================================*/
	DefinitionList : function (element, options) {
		Fool.Sortable.interface.apply(this, arguments);
		
		// overwrite interface's getItems method
		this.getItems = function (nodeList) {
			var items = this.items, Item = Fool.util.sortable.Item;
			$A(nodeList).each(function(v,i){items[items.length] = new Item(v, v.nextSibling);});
			return this.items;
		};

		// overwrite interface's render method
		this.render = function () {
			var target = this.element, children = target.childNodes, idx = 0;
			function reorder (v, i) {
				target.insertBefore(v.element, children[idx++]);
				target.insertBefore(v.associatedElement, children[idx++])
			}
			this.items.each(reorder);
		};

		this.getItems(element.getElementsByTagName("DT"));
	},

	
	/*
	* Fool.Sortable.Table  - TABLE
	*===============================================================================================*/
	Table : function (element, options) {
		Fool.Sortable.interface.apply(this, arguments);		
    
		var tbody = this.element.getElementsByTagName("TBODY")[0], rows = tbody.getElementsByTagName("TR");
		
		// ovewrite interface's getItems method
		this.getItems = function () {
			this.items = [];
			var Item = Fool.Sortable.Item, items = this.items;
			var idx = (this.options && !isNaN(this.options.idx)) ? this.options.idx : 0

      $A(rows).each(function (row, i) {
				var td = row.childNodes[idx];
				items[items.length] = new Item(td, row);

				if (Element.hasClassName(td, "date")) {
					// override item.text, use abbr.title instead of td.innerHTML
					items.last().text = td.getElementsByTagName("abbr")[0].title;
				}

				td = null;
			});

			//tbody = null;
			return this.items;
		};

		// overwrite inteface's render method
		this.render = function () {
			var idx = 0;
			this.items.each(function (v, i) { tbody.insertBefore(v.associatedElement, rows[idx++]); });
		};
		
		this.getItems();
	},


	/*
	* Fool.Sortable.Item - Generic wrapper for sorting elements
	*===============================================================================================*/
	Item : function (element, associatedElement) {	
		// clean value - remove $ and %
		var value = element.innerHTML.escapeHTML().toLowerCase();
		value = value.replace(/%|\$/g,""); // 
		
		this.text = (isNaN(parseFloat(value))) ? value : parseFloat(value);
		this.element = element;
		this.associatedElement = associatedElement || null;
		this.html = element.innerHTML;
		this.toString = function () {
			return (isNaN(this.text)) ? this.text : parseFloat(this.text);
		};
	}
};