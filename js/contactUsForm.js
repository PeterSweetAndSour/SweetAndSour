
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

	var _loadReCAPTCHA = function () {
		var reCAPTCHAWrapper = document.querySelector("#reCAPTCHAWrapper");
		if(!reCAPTCHAWrapper) {
			return;
		}

		loadScript('https://www.google.com/recaptcha/api.js?onload=renderReCAPTCHA&render=explicit')
			.then(() => {})
			.catch(() => console.error('Something went wrong.'));
	};

	
	var _renderReCAPTCHA = function() {
		var reCAPTCHAWrapper = document.querySelector("#reCAPTCHAWrapper");
		var siteKey = reCAPTCHAWrapper.dataset.siteKey;
		grecaptcha.render('reCAPTCHAWrapper', {
			'sitekey' : siteKey
		});
	}

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
		enableSubmitButton: _enableSubmitButton
	};  
})(); // the paranthesis will execute the function immediately. Do not remove.


document.addEventListener("DOMContentLoaded", function(){
	ContactUs.initialize();
});


// ------------------------------------------------------------------------------------------------------------------------------------------------
// I am baffled why I can't move the contents of renderReCAPTCHA inside the .then() inside _loadReCAPTCHA and why
// data-callback="enableSubmitButton" in dsp_contactUs.php can't use data-callback="ContactUs.enableSubmitButton".
// ------------------------------------------------------------------------------------------------------------------------------------------------
var renderReCAPTCHA = function() {
	var reCAPTCHAWrapper = document.querySelector("#reCAPTCHAWrapper");
	var siteKey = reCAPTCHAWrapper.dataset.siteKey;
	grecaptcha.render('reCAPTCHAWrapper', {
		'sitekey' : siteKey
	});
}

var enableSubmitButton = function() {
	document.querySelector("#submitBtn").removeAttribute("disabled");
};

