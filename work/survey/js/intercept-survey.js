const searchResults = (function() {

	let siteName = "";

	// currentScript does not work with callback functions so is called at the end of this file
	const _setSiteName = function() {
		var queryString = document.currentScript.src.substring( document.currentScript.src.indexOf("?") ); 
		var urlParams = new URLSearchParams( queryString );
		siteName = urlParams.get("site");
	}

	const _checkPriorInteractionCookie = function() {
		const cookieStr = document.cookie;
		const cookies = cookieStr.split(";");
		let interceptSurveyCookie = null;
		for(let i=0; i < cookies.length; i++) {
			const cookie = cookies[i].trim();
			if((siteName === "careers" && cookie.startsWith("interceptSurvey=")) ||
			   (siteName === "aggregator" && cookie.startsWith("interceptSurveyAggregator="))) {
				interceptSurveyCookie = cookie;
				break;
			}
		}

		if(interceptSurveyCookie == null) {
			_initiatizeSurvey();
		}
		else {
			console.log(interceptSurveyCookie);
		}
	};

	const _setPriorInteractionCookie = function() {
		const now = new Date();
		const expiresDate = new Date();
		expiresDate.setDate(3650); //expires in 10 years

		let cookieName = "";
		if(siteName === "careers") {
			cookieName = "interceptSurvey";
		}
		else if(siteName === "aggregator") {
			cookieName = "interceptSurveyAggregator";
		}

		let cookieStr = cookieName + "=" + now.toUTCString() + ";";
		cookieStr += "Domain=" + window.location.hostname	+ ";";
		cookieStr += "Expires=" + expiresDate.toUTCString()	+ ";";
		cookieStr += "SameSite=Lax";

		document.cookie = cookieStr;
	};

	const _initiatizeSurvey = function() {
		_insertStylesheet();
		_displaySurvey();
	};

	const _insertStylesheet = function() {
		let head = document.querySelector("head");
		let link = document.createElement("link");
		link.rel = "stylesheet";
		link.type = "text/css";
		link.href = "./css/intercept-survey.css";
		head.appendChild(link);
	};

	const _displaySurvey = function() {
		let surveyHtmlFile = "";
		if(siteName === "careers") {
			surveyHtmlFile = "intercept-survey.html";
		}
		else if(siteName === "aggregator") {
			surveyHtmlFile = "intercept-survey-aggregator.html";
		}
 
		fetch("./" + surveyHtmlFile).then((response) => {
			return response.text();
		}).then((html) => {
			let surveyContainer = document.createElement("div");
			surveyContainer.innerHTML = html;
			let body = document.querySelector("body");
			body.appendChild(surveyContainer);
			_setEventListeners();
			_setIPAddress();
		}).catch((err => {
			console.warn("Something went wrong.", err);
		}));
	};


	const _setEventListeners = function() {
		_setFeedbackButtonEventListener();
		_setInterceptSurveyEventListeners();
	};

	const _setFeedbackButtonEventListener = function() {
		const feedbackButton = document.querySelector("#feedbackButton");
		feedbackButton.addEventListener("click", (evt) => {
			const interceptSurvey = document.querySelector("#interceptSurvey"); 
			interceptSurvey.classList.remove("hidden");
			feedbackButton.classList.add("hidden");
		});
	};

	const _setInterceptSurveyEventListeners = function() {
		const interceptSurvey = document.querySelector("#interceptSurvey"); 
		interceptSurvey.addEventListener("click", (evt) => {
			let target = evt.target;
			if(target.id === "closeSurveyBtn" || target.id === "exitSurvey") {
				_closeSurvey();
			}
			else if(target.id === "goToSurvey") {
				_displayPanel(2);
			}
			else if(target.id.startsWith("taskID")) {
				let otherTask = document.querySelector("#otherTask");

				if(target.id.endsWith("4")) {
					otherTask.classList.remove("hidden");
					otherTaskRadio = document.querySelector("#taskIDRadio_4");
					otherTaskRadio.checked = true;

					otherTaskTextInput = document.querySelector("#otherTask");
					otherTaskTextInput.focus();

					// Enable the Next button
					let nextButton = document.querySelector("#goToSurveyPanel_3");
					nextButton.classList.remove("disabled");
					nextButton.removeAttribute("disabled");
				}
				else {
					otherTask.value = "";
					otherTask.classList.add("hidden");
					_displayPanel(3);
				}
			}

			else if(target.id.startsWith("goToSurveyPanel_6")) {
				const aggregatorCommentsQuestion = document.querySelector("#aggregatorCommentsQuestion");
				const foundInfoHiddenFormVar = document.querySelector("#foundInfo");
				if(target.id.endsWith("yes")) {
					aggregatorCommentsQuestion.textContent = "What is the most helpful in getting you the information you need?";
					foundInfoHiddenFormVar.value = "yes";
				}
				else {
					aggregatorCommentsQuestion.textContent = "What could we do on this page to help you find and apply for a job?";
					foundInfoHiddenFormVar.value = "no";
				}
				_displayPanel(6);
			}
			else if(target.id === "goToSurveyPanel_3") {
				_displayPanel(3);
			}
			else if(target.id.startsWith("difficulty_")) {
				const rating = target.id.substr(target.id.length - 1, 1);
				const ratingField = document.querySelector("#difficultyID_" + rating);
				ratingField.checked = true;

				_displayPanel(4);
			}
			else if(target.id === "submitSurveyBtn") {
				_displayPanel(5);
				const closeSurveyBtn = document.querySelector("#closeSurveyBtn");
				closeSurveyBtn.classList.add("hidden");

				window.setTimeout(() => { 
					_closeSurvey();
				}, 1500);

				// POST the form data
				const interceptSurveyForm2 = document.querySelector("#interceptSurveyForm"); 
				const formActionUrl = interceptSurveyForm2.getAttribute("action"); // + "?XDEBUG_SESSION";
				formData = new FormData(interceptSurveyForm2);

				fetch(formActionUrl, {
					method: 'POST',
					body: formData,
				})		
				.catch((error) => {
					console.warn(error);
				});
			}

			// Make sure that the form doesn't submit itself
			const interceptSurveyForm = document.querySelector("#interceptSurveyForm");
			interceptSurveyForm.addEventListener("submit", (evt) => { evt.preventDefault(); });
		});

		const surveyTextArea = document.querySelector("#comments");
		const charCountIndicator = document.querySelector("#charCountIndicator");
		let charCount = 0;
		surveyTextArea.addEventListener("input", () => {
			charCount = surveyTextArea.value.length;
			charCountIndicator.innerHTML = charCount;
		});
	};

	const _closeSurvey = function() {
		const interceptSurvey = document.querySelector("#interceptSurvey");
		interceptSurvey.classList.add("hidden");

		const feedbackButton = document.querySelector("#feedbackButton");
		feedbackButton.classList.add("hidden");

		_setPriorInteractionCookie();
	};

	const _displayPanel = function(panelToShow) {
		let surveyPanels = document.querySelectorAll(".surveyPanel");
		for(let i=0; i<surveyPanels.length; i++) {
			let panel = surveyPanels[i];
			let panelNumber = panel.id.replace("surveyPanel_", "");

			if(panelNumber == panelToShow) {
				panel.classList.remove("hidden");
			}
			else {
				panel.classList.add("hidden");
			}
		}
	};

	const _setIPAddress = function() {
		fetch("https://api.ipify.org?format=json")
		.then(response => response.json())
		.then(data => {
				const IPAddressField = document.querySelector("#IPAddress");
				IPAddressField.value = data.ip;
				//console.log("IP address: " + data.ip);
		})
		.catch(error => {
				//console.error("Error fetching IP address:", error);
				return "n/a";
		});
	};

	const _setFeedbackButtonPosition = function() {
		const feedbackButton = document.querySelector("#feedbackButton");

		if (window.scrollY) {
			feedbackButton.classList.remove("expanded");
		}
		else {
			feedbackButton.classList.add("expanded");
		}
	};

	const _initialize = function() {
		_checkPriorInteractionCookie();
	};

	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Public methods
	return {  
		initialize: _initialize,
		setFeedbackButton: _setFeedbackButtonPosition,
		setSiteName: _setSiteName
	};  
})(); // the parenthesis will execute the function immediately. Do not remove.

document.addEventListener("DOMContentLoaded", function(){
	searchResults.initialize();
});

document.addEventListener("scroll", function(){
	searchResults.setFeedbackButton();
});

searchResults.setSiteName();