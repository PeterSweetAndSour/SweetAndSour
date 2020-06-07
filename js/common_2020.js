var SweetAndSour = (function() {

	var _loadAppropriateMenu = function(isMobile) {
		var mobileNavigationSet,
			desktopNavigationSet;

		if(isMobile) {
			MobileNavigation.setMobileMenue();
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

	var _setOverlayHeightAndPosition = function(isMobile) {
		var overlay = document.querySelector("#overlay");
		if(isMobile) {
			overlay.style.height = document.body.scrollHeight + "px";
			overlay.style.top = "83px";
		}
		else {
			overlay.style.height = "281px";
			overlay.style.top = "105px";
		}
	};

	var _initialize = function() {
		var mediaQuery = window.matchMedia( "(max-width: 959px)" );
		var isMobile = mediaQuery.matches;

		_loadAppropriateMenu(isMobile);
		_setOverlayHeightAndPosition(isMobile);
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
