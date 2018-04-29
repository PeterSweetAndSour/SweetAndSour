/**************************************************************

	Script	: Image Menu
	Version	: 2.2
	Authors	: Samuel Birch
	Desc	: 
	Licence	: Open Source MIT Licence
	Options:
		onOpen
		    a function to execute when an item is clicked open. If there is an href within the li then that is passed to the function.
		onClose
		    same as above but when clicked closed.
		openWidth
		    width in px of the items when rolled over. default: 200
		closedWidth
		   width in px of the items when another is expanded
		transition
		    default: Fx.Transitions.quadOut
		duration
		    the length of the transition. default: 400
		open
		    the id or index of the item to open on start. default: null
		border
		    a px value to tweak the widths when an item is open. default: 0
		stopEventEvent
		   set to false if using submenus. default:true

**************************************************************/

var ImageMenu = new Class({

	Implements: Options,

	options: {
		onOpen: false,
		onClose: Class.empty,
		openWidth: 200, // Default not used - see act_constructMenu.php
		closedWidth:20, // Default not used - see act_constructMenu.php
		transition: Fx.Transitions.Quad.easeOut,
		duration: 400,
		open: null,
		border: 0,
		stopEvent: false
	},
	
	initialize: function(elements, options){
		this.setOptions(options);  // setOptions is in Mootools http://docs.mootools.net/Class/Class.Extras#Options:setOptions
		
		//this.setOptions(this.getOptions(), options);  // setOptions is in Mootools http://docs.mootools.net/Class/Class.Extras#Options:setOptions

		this.elements = $$(elements);

		this.widths = {};
		this.widths.openSelected = this.options.openWidth;
		if(this.options.closedWidth) {
			this.widths.openOthers = this.options.closedWidth;
		}
		else {
			this.widths.closed = this.elements[0].getStyle('width').toInt();
			this.widths.openOthers = Math.round(((this.widths.closed*this.elements.length) - (this.widths.openSelected+this.options.border)) / (this.elements.length-1));
		}
		//console.log("this.widths.closed: " + this.widths.closed + ", openOthers: " + this.widths.openOthers + ", this.widths.openSelected: " + this.widths.openSelected);
	
		this.fx = new Fx.Elements(
			this.elements, 
			{
				wait: false, 
				duration: this.options.duration, 
				transition: this.options.transition
			}
		);
		
		this.elements.each(function(element,i){  // remember that 'element' is a LI, not an A
			element.addEvent('mouseover', function(e){
				if(this.options.stopEvent) {
					new Event(e).stop();
				}
				this.reset(i);
				
			}.bind(this));
			
			element.addEvent('mouseout', function(e){  // same here too
				if(this.options.stopEvent) {
					new Event(e).stop();
				}
				this.reset(this.options.open);
				
			}.bind(this));
			
			var obj = this; 
			
			if (obj.options.onOpen){

				element.getElement("span").addEvent('click', function(e){ // Looking for the click event on the SPAN not the A because we are using the Gilder/Levin image replacement method (www.mezzoblue.com/tests/revised-image-replacement/#gilderlevin) !
					if(obj.options.stopEvent) {
						new Event(e).stop(); 
					}
					if(obj.options.open == i){ // clicking on the one that is already open
						obj.options.open = null;
						obj.options.onClose(this.parentNode.href, i);
					}
					else {
						obj.options.open = i;
						obj.options.onOpen(this.parentNode.href, i);
					}
				});
			}
			
		}.bind(this));
		
		// Expand one menu item on page load
		if (this.options.open !== null){ // had to add "!= null" or it would not open at 0 as it interpreted that as false
			if (typeOf(this.options.open) == 'number'){
				this.reset(this.options.open);
			}
			else {
				this.elements.each(
					function(element,i){
						if(element.id == this.options.open){
							this.reset(i);
						}
					},this
				);
			}
		}

	}, // end initialize
	
	reset: function(num){
		var width;
		if (typeOf(num) == 'number'){
			width = this.widths.openOthers;
			if(num+1 == this.elements.length){
				width += this.options.border;
			}
		}
		else {
			width = this.widths.closed;
		}
		
		var obj = {};
		
		this.elements.each(function(el,i){
			var w = width;
			if (i == this.elements.length-1){
				w = width+5;
			}
			obj[i] = {'width': w};
		}.bind(this));
		
		if (typeOf(num) == 'number'){
			obj[num] = {'width': this.widths.openSelected};
		}
		this.fx.start(obj);
	}
	
});

//ImageMenu.implement(new Options);
//ImageMenu.implement(new Events);


/******************************************************************************
Supplementary code for sub-menus - Peter V

Note that there are some small changes to ImageMenu above
* Pass in another option stopEvent (=false) to avoid killing the mouse in/out event
* Passing in <li> not <a> since mouse still needs to be "over" top-level menu to keep it open
   while on the secondary/tertiary menus.

1. Mouseover level 1 anchors needs to ...
   * add class name 'off' to level 1 LI with class of 'selected'
   * set class of hovered-over LI to 'on'
	
2. Mouseout level 1 needs to undo 1 above

3 Mouseover level 2 anchors needs to ...
   * add class name 'off to level 2 LI with class name of 'selected (if there is one)
   * set class of hovered-over LI to 'on'
   * set class name of LI before hovered-over LI (if there is one) to 'beforeSelected'
	
4. Mouseout level 2 needs to undo 3 above

At least for the moment, I will handle level 1 and level 2 separately for simplicity.
	
*******************************************************************************/


var IMSubNav = (function() {
	// PRIVATE member variables and functions
	var level1Selected;
	var level2Selected = null;
	var lastLevel1Over = null;
	var lastLevel2Over = null;
	var level2Previous = null;

	
	// Now the PUBLIC stuff
	return {
		prepare : function () { // called immediately after menu is written out - see act_constructMenu.php
			// Get currently selected level 1 list item
			level1Selected = $("imageMenu").getFirst("ul").getElement("li.selected"); 
			
			// Get the primary (photo) navigation list items. Note that we had to tell ImageMenu not to cancel event or this won't fire
			var level1ListItems = $("imageMenu").getFirst("ul").getChildren(); 
			level1ListItems.each(
				function (listItem1, index) {
					listItem1.addEvent("mouseover", this.activate1.bind(this, listItem1));
					listItem1.addEvent("mouseout", this.restore1.bind(this, listItem1));
					listItem1.addEvent("click", this.activate1.bind(this, listItem1));
				},
				this
			);
			
			// Do the same for all second level list items
			// Get currently selected level 2 list item - if there is a level 2 menu
			level2Selected = level1Selected.getElement("li.selected");

			var level2Lists = $$("ul.menu2");
			var level2ListItems = [];
			level2Lists.each(
				function(list) {
					level2ListItems.append(list.getChildren("li"));
				}
			);
			
			level2ListItems.each(
				function (listItem2, index) {
					listItem2.addEvent("mouseover", this.activate2.bind(this, listItem2));
					listItem2.addEvent("mouseout", this.restore2.bind(this, listItem2));
					listItem2.addEvent("click", this.activate2.bind(this, listItem2));
				},
				this
			);
			
			
		},
		
		// --------------------------------------------------------------------------
		// Level 1 
		activate1 : function (listItem) {
			if(listItem != level1Selected) {
				level1Selected.addClass("off");
				listItem.addClass("on");
				lastLevel1Over = listItem;
			}
		},
		
		restore1 : function () {
			level1Selected.removeClass("off");
			if(lastLevel1Over) {
				lastLevel1Over.removeClass("on");
			}
		},
		
		// --------------------------------------------------------------------------
		// Level 2
		activate2 : function (listItem) {
			if(listItem != level2Selected) {
				if(level2Selected) {
					level2Selected.addClass("off");
				}		
				
				listItem.addClass("on");
				lastLevel2Over = listItem;
				level2Previous = listItem.getPrevious("li");
				if(level2Previous) {
					level2Previous.addClass("beforeSelected");
				}
			}
		},
		
		restore2 : function () {
			if(level2Selected) {
				level2Selected.removeClass("off");
			}
			if(lastLevel2Over) {
				lastLevel2Over.removeClass("on");
				if(level2Previous) {
					level2Previous.removeClass("beforeSelected");
					level2Previous = null;
				}
			}
		}
	};
})(); // "()" means it is self-executing - and runs only once





















