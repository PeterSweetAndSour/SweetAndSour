/* -----------------------------------------------------------------------------------------
Javascript for the menu. See contactUsForm.js for the script on the Contact Us page.
Uses the Revealing Module Pattern https://coryrylan.com/blog/javascript-module-pattern-basics
------------------------------------------------------------------------------------------ */
var SweetAndSour = (function() {

	var _loadAppropriateMenu = function(isMobile) {
		var mobileNavigationSet,
			desktopNavigationSet;

		var nav = document.querySelector("nav");
		if(!nav) {
			return;
		}

		if(isMobile) {
			MobileNavigation.setMobileMenu();
			mobileNavigationSet = true;
			if(desktopNavigationSet) {
				DesktopNavigation.unsetDesktopMenu();
			}
		}
		else {
			DesktopNavigation.setDesktopMenu();
			desktopNavigationSet = true;
			if(mobileNavigationSet) {
				MobileNavigation.unsetMobileMenu();
			}
		}
	};

	var _setMenuOverlayHeight = function(isMobile) {
		var overlay = document.querySelector("#menuOverlay");
		if(isMobile) {
			overlay.style.height = document.body.scrollHeight + "px";
		}
	};

	var _initialize = function() {
		var mediaQuery = window.matchMedia( "(max-width: 959px)" );
		var isMobile = mediaQuery.matches;

		_loadAppropriateMenu(isMobile);
		_setMenuOverlayHeight(isMobile);
	};

	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Public methods
	return {  
		initialize : _initialize
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.


document.addEventListener("DOMContentLoaded", function(){
	SweetAndSour.initialize();
});

window.addEventListener("resize", function() {
	SweetAndSour.initialize();
});
