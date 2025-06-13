/* -----------------------------------------------------------------------------------------
		Javascript to load 3rd and 4th level navigation
		Uses the Revealing Module Pattern https://coryrylan.com/blog/javascript-module-pattern-basics
		------------------------------------------------------------------------------------------ */
		var Squarespace = (function() { 
			/* Create an object that shows the structure of the navigation.
				There is no need to provide the URLs for the primary and secondary menu items since these can
				be created within Squarespace using the Main Navigation builder under Pages and adding "Dropdowns"
				for secondary navigation. 
				
				Set up the 3rd and 4th level pages as "Unlinked" and click the gear icons to enter the page settings
				to see the generated "URL SLUG". The URLs in the JSON must match those that Squarespace generates 
				(or you modify).
				) */
			const _navigationObj = 
				[
					{"navigationTitle": "Home"},

					{"navigationTitle": "About", 
						"subMenu": [
							{"navigationTitle": "About the Grant", "url": "/about-the-grant"},
							{"navigationTitle": "About the i-Team", "url": "/about-the-i-team"}
						]
					},
					
					{"navigationTitle": "Work", 
						"subMenu": [
							{"navigationTitle": "Permitting",
								"subMenu": [
									{"navigationTitle": "Permitting Understanding Phase", "url": "/permitting-understand-phase"},
									{"navigationTitle": "Permitting Generate Ideas", "url": "/permitting-generate-ideas"},
									{"navigationTitle": "Permitting Deliver Solutions", "url": "/permitting-deliver-solutions"}
								]
							},

							{"navigationTitle": "Hiring", 
								"url": "/hiring",
								"subMenu": [
									{"navigationTitle": "Hiring Understanding Phase", "url": "/hiring-understand-phase"},
									{"navigationTitle": "Hiring Generate Ideas", "url": "/hiring-generate-ideas"},
									{"navigationTitle": "Hiring Deliver Solutions", "url": "/hiring-deliver-solutions",
										"subMenu": [
											{"navigationTitle": "HBCU Public Service Program", "url": "/hbcu-public-service-program"},
											{"navigationTitle": "Vacancy Announcements", "url": "/vacancy-announcements"},
											{"navigationTitle": "Applicant Tracking System", "url": "/applicant-tracking-system"},
											{"navigationTitle": "FEMS", "url": "/fems"},
											{"navigationTitle": "Gratitude Powers DC", "url": "/gratitude-powers-dc"},
											{"navigationTitle": "Jobs Aggregator Page", "url": "/jobs-aggregator-page"}
										]
									}
								]
							},

							{"navigationTitle": "DC.gov Redesign",
								"subMenu": [
									{"navigationTitle": "DC.gov Understanding Phase", "url": "/dcgov-understand-phase"},
									{"navigationTitle": "DC.gov Generate Ideas", "url": "/dcgov-generate-ideas"},
									{"navigationTitle": "DC.gov Deliver Solutions", "url": "/dcgov-deliver-solutions"}
								]
							}
						]
					},

					{"navigationTitle": "Deliverables"},

					{"navigationTitle": "Acknowledgements", 
						"subMenu": [
							{"navigationTitle": "OCTO"},
							{"navigationTitle": "EOM"},
							{"navigationTitle": "BCPI Partner Coaches"},
							{"navigationTitle": "DC Government Partner Agencies"							}
						]
					}
				];

				// From https://stackoverflow.com/questions/722668/traverse-all-the-nodes-of-a-json-object-tree-with-javascript
				/*
				const traverse = function*(o, path=[]) {
					for (var i in o) {
							const itemPath = path.concat(i);
							yield [i,o[i],itemPath,o];
							if (o[i] !== null && typeof(o[i])=="object") {
									//going one step down in the object tree!!
									yield* traverse(o[i], itemPath);
							}
					}
				}
				*/

				const _updateDesktopNavigation = function(navElement) {

				/*
				for(var [key, value, path, parent] of traverse(_navigationObj)) {
					// do something here with each key and value
					console.log("key: " + key + ", value: " + value + ", path: " + path + ", parent: " + parent); //
				}
				*/
			};

			const _updateMobileNavigation = function(navElement) {

			};

			

			const _initialize = function() {
				let navElements = document.querySelectorAll("nav.header-nav-list");

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



