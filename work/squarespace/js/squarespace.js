
/* -----------------------------------------------------------------------------------------
	Uses the Revealing Module Pattern https://coryrylan.com/blog/javascript-module-pattern-basics
	------------------------------------------------------------------------------------------ */
	var Squarespace = (function() { 

		const _hiringSubmenuObj = [
			{"navigationTitle": "HBCU Public Service Program", "url": "/hbcu-public-service-program"},
			{"navigationTitle": "Vacancy Announcements", "url": "/vacancy-announcements"},
			{"navigationTitle": "Applicant Tracking System", "url": "/applicant-tracking-system"},
			{"navigationTitle": "FEMS", "url": "/fems"},
			{"navigationTitle": "Gratitude Powers DC", "url": "/gratitude-powers-dc"},
			{"navigationTitle": "Jobs Aggregator Page", "url": "/jobs-aggregator-page"}
		];

		const _updateDesktopNavigation = function(navElement) {
			_updateNavigation(navElement);
		}

		const _updateMobileNavigation = function(navElement) {
			_updateNavigation(navElement);
		};

		const _updateNavigation = function(navElement) {
			// Find the Hiring menu item
			let hiringMenuDiv = navElement.querySelector(".header-nav-item:nth-child(3) > .header-nav-folder-content > .header-nav-folder-item:nth-child(2)");
			
			let hiringSubmenu = document.createElement("div");
			hiringSubmenu.classList.add("header-nav-folder-content-submenu");
			hiringMenuDiv.appendChild(hiringSubmenu);
			
			for (let submenuItemObj of _hiringSubmenuObj) {
				let headerNavFolderItem = document.createElement("div");
				headerNavFolderItem.classList.add("header-nav-folder-item");
				
				let submenuLink = document.createElement("a");
				submenuLink.href = submenuItemObj.url;
				headerNavFolderItem.appendChild(submenuLink);
				
				let submenuLinkSpan = document.createElement("span");
				submenuLinkSpan.classList.add("header-nav-folder-item-content");
				submenuLink.appendChild(submenuLinkSpan);
				
				let submenuLinkText = document.createTextNode(submenuItemObj.navigationTitle);
				submenuLinkSpan.appendChild(submenuLinkText);

				hiringSubmenu.appendChild(headerNavFolderItem);
			}
		};


	const _initialize = function() {
			let navElements = document.querySelectorAll("nav.header-nav-list");

			// Desktop and mobile navs are the same down to the classes. Even IDs are duplicated!
			_updateDesktopNavigation(navElements[0]);
			_updateMobileNavigation(navElements[1]);
		};


		// ------------------------------------------------------------------------------------------------------------------------------------------------
		// Public methods
		return {  
			initialize : _initialize
		};  
	})(); // the paranthesis will execute the function immediately. Do not remove.


	document.addEventListener("DOMContentLoaded", function(){
		Squarespace.initialize();
	});

