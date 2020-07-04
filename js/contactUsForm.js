
/**
 * Loads a JavaScript file and returns a Promise for when it is loaded
 * From https://aaronsmith.online/easily-load-an-external-script-using-javascript/
 */
const loadScript = src => {
	return new Promise((resolve, reject) => {
	  const script = document.createElement('script')
	  script.type = 'text/javascript'
	  script.onload = resolve
	  script.onerror = reject
	  script.src = src
	  document.head.append(script)
	})
}
  
var ContactUs = (function() { 

	var _activateFormWhenAppropriate = function() {
		const continueBtn = document.querySelector("#continueBtn");
		if(continueBtn) {
			continueBtn.addEventListener("click", function() {
				_activateForm();
			});
		}
		else { //
			_activateForm();
		}

	};

	var _addFocusClassToTextAreaLabel = function(event) {
		const msgTextArea = event.target;
		const msgTextAreaLabel = msgTextArea.parentNode.querySelector("label");
		msgTextAreaLabel.classList.add("focused");
	};

	var _removeFocusClassFromTextAreaLabel = function(event) {
		const msgTextArea = event.target;
		const msgTextAreaLabel = msgTextArea.parentNode.querySelector("label");
		
		if(!msgTextArea.value) {
			msgTextAreaLabel.classList.remove("focused");
		}
	};

	var _updateCharacterCount = function(event) {
		const target = event.target;
		const maxLength = parseInt(target.getAttribute("maxlength"), 10);
		const currentLength = target.value.length;
		const feedbackWrapper = document.querySelector("#remainingChars")
		if (currentLength >= maxLength) {
			feedbackWrapper.innerHTML = "You have reached the maximum number of characters.";
		}
		else {
			feedbackWrapper.innerHTML = (maxLength - currentLength) + " characters left";
		}
	};

	var _hideGDPRNotice = function() {
		var GDPRNotice = document.querySelector("#GDPRNotice");
		if(GDPRNotice) {
			GDPRNotice.classList.add("hidden");
		}
	};

	var _revealForm = function() {
		document.querySelector("#contactUsForm").classList.remove("hidden");
	};

	var _enableSubmitButton = function() {
		document.querySelector("#submitBtn").removeAttribute("disabled");
	};

	var _disableSubmitButton = function() {
		document.querySelector("#submitBtn").setAttribute("disabled", null);
	};

	var _renderReCAPTCHA = function() {
		var reCAPTCHAWrapper = document.querySelector("#reCAPTCHAWrapper");
		var siteKey = reCAPTCHAWrapper.dataset.siteKey;

		grecaptcha.render(reCAPTCHAWrapper, {
			'sitekey' : siteKey,
			'callback': _enableSubmitButton,
			'expired-callback':_disableSubmitButton
		});
	};

	var _loadReCAPTCHA = function () {
		var reCAPTCHAWrapper = document.querySelector("#reCAPTCHAWrapper");
		if(!reCAPTCHAWrapper) {
			return;
		}

		// You must include onload=renderReCAPTCHA in the URL rather than calling renderReCAPTCHA inside .then().
		// Google must modify the packaged returned when that information is made with the request.
		// Also, you must call global function; perhaps it is not surprising that _renderReCAPTCHA does not work
		// since it is private but neither does ContactUs.renderReCAPTCHA.
		// https://stackoverflow.com/questions/27776964/new-google-recaptcha-javascript-namespace-callback.
		loadScript('https://www.google.com/recaptcha/api.js?onload=renderReCAPTCHA&render=explicit')
			.catch(() => { console.error('Something went wrong and the reCAPTCHA did not load.'); });
	};

	var _setTextAreaEventListeners = function() {
		const msgTextArea = document.querySelector("#msgText");
	
		msgTextArea.addEventListener("focus", event => {
			_addFocusClassToTextAreaLabel(event);
		});
	
		msgTextArea.addEventListener("blur", event => {
			_removeFocusClassFromTextAreaLabel(event);
		});
	
		msgTextArea.addEventListener("input", event => {
			_updateCharacterCount(event);
		});
	};

	var _activateForm = function() {
		_hideGDPRNotice();
		_revealForm();
		_loadReCAPTCHA();
		_setTextAreaEventListeners();
	}

	var _initialize = function() {
		var contactUsForm = document.querySelector("#contactUsForm");
		if(contactUsForm) {
			_activateFormWhenAppropriate();
		}	
	};


	// ------------------------------------------------------------------------------------------------------------------------------------------------
	// Public methods
	return {  
		initialize : _initialize,
		loadReCAPTCHA: _loadReCAPTCHA,
		renderReCAPTCHA: _renderReCAPTCHA,
		enableSubmitButton: _enableSubmitButton,
		disableSubmitButton: _disableSubmitButton
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.


document.addEventListener("DOMContentLoaded", function(){
	ContactUs.initialize();
});


// ------------------------------------------------------------------------------------------------------------------------------------------------
// Creating a global function for the benefit of reCAPTCHA. See details above.
// ------------------------------------------------------------------------------------------------------------------------------------------------
var renderReCAPTCHA = function() {
	ContactUs.renderReCAPTCHA();
};
// This works too (as you would expect but you can't set ...api.js?onload=ContactUs.renderReCAPTCHA&...)
// window.renderReCAPTCHA = ContactUs.renderReCAPTCHA;



