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
  
/* -----------------------------------------------------------------------------------------
Javascript for the form
------------------------------------------------------------------------------------------ */
var ContactUs = (function() { 

	var formData = {};

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
		msgTextAreaLabel.classList.remove("focused");
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
			'callback': _setReCAPTCHAValidity,
			'expired-callback': _revokeReCAPTCHAValidity
		});
	};

	var _setReCAPTCHAValidity = function() {
		var reCAPTCHAIsValid = document.querySelector("#reCAPTCHAIsValid");
		reCAPTCHAIsValid.value = true;
		_checkFormValidity();
	};

	var _revokeReCAPTCHAValidity = function() {
		var reCAPTCHAIsValid = document.querySelector("#reCAPTCHAIsValid");
		reCAPTCHAIsValid.value = false;
		_disableSubmitButton();
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

	var _setFormFieldEventListeners = function() {
		var form = document.querySelector("#contactUsForm");
		var senderNameField = form.querySelector("#senderName");
		var senderEmailField = form.querySelector("#senderEmail");
		var msgSubjectField = form.querySelector("#msgSubject");
		var msgTextArea = form.querySelector("#msgText");
		var challengeField = form.querySelector("#challenge");

		senderNameField.addEventListener("blur", event => {
			senderNameField.reportValidity();
			_checkFormValidity();
		});
		senderEmailField.addEventListener("blur", event => {
			senderEmailField.reportValidity();
			_checkFormValidity();
		});
		msgSubjectField.addEventListener("blur", event => {
			msgSubjectField.reportValidity();
			_checkFormValidity();
		});
	
		msgTextArea.addEventListener("focus", event => {
			_addFocusClassToTextAreaLabel(event);
		});
	
		msgTextArea.addEventListener("blur", event => {
			if(!msgTextArea.value) {
				_removeFocusClassFromTextAreaLabel(event);
			}
			msgTextArea.reportValidity();
			_checkFormValidity();
		});
	
		msgTextArea.addEventListener("input", event => {
			_updateCharacterCount(event);
		});

		challengeField.addEventListener("blur", event => {
			var challengeField = document.querySelector("#challenge");
			if(challengeField.value === "") {
				challengeField.reportValidity();
				return;
			}

			var correctAnswer = _checkChallengeAnswer();
			var challengeMsg = document.querySelector("#challengeMsg");

			if(correctAnswer) {
				challengeField.classList.remove("errorMsg");
				challengeMsg.classList.add("hidden");
				_checkFormValidity();
			}
			else {
				challengeField.classList.add("errorMsg");
				challengeMsg.classList.remove("hidden");
			}
		});
	};

	var _checkChallengeAnswer = function() {
		var form = document.querySelector("#contactUsForm");
		var challengeField = form.querySelector("#challenge");
		var value = challengeField.value.toLowerCase().trim()
		if(value === "south australia"){
			return true;
		}
		else {
			return false;
		}
	};

	var _checkFormValidity = function() {
		var contactUsForm = document.querySelector("#contactUsForm");
		var senderNameField = contactUsForm.querySelector("#senderName");
		var senderEmailField = contactUsForm.querySelector("#senderEmail");
		var msgSubjectField = contactUsForm.querySelector("#msgSubject");
		var msgTextArea = contactUsForm.querySelector("#msgText");
		var reCAPTCHAIsValid = document.querySelector("#reCAPTCHAIsValid");
		var challengeField = document.querySelector("#challenge");

		if(senderNameField.checkValidity() && 
			senderEmailField.checkValidity() && 
			msgSubjectField.checkValidity() && 
			msgTextArea.checkValidity() && 
			reCAPTCHAIsValid.value &&
			challengeField.checkValidity() &&
			_checkChallengeAnswer()
		) {
			formData = new FormData(contactUsForm);
			_enableSubmitButton();
		}
	};

	var _setSubmitBtnEventListener = function() {
		const submitBtn = document.querySelector("#submitBtn");
		submitBtn.addEventListener("click", event => {
			_disableSubmitBtn();
			event.preventDefault();
			_submitForm();
		});
	};

	var _disableSubmitBtn = function() {
		document.querySelector("#submitSpinnerWrapper").classList.remove("hidden");
	};
	var _enableSubmitBtn = function() {
		document.querySelector("#submitSpinnerWrapper").classList.add("hidden");
	};

	var _submitForm = function() { 
		var noticeSuccess = document.querySelector("#noticeSuccess");
		var noticeWarning = document.querySelector("#noticeWarning");

		fetch("index.php?fuseAction=sendEmail", {
			method: 'POST',
			body: formData,
		})
		.then(response => response.json())
		.then(data => {
			if(data.status === "success") {
				noticeSuccess.innerHTML = "<h2>Success</h2><p>" + data.text + "</p>";
				noticeSuccess.classList.remove("hidden");
				noticeWarning.classList.add("hidden");
				document.querySelector("#contactUsFormWrapper").classList.add("hidden");
			}
			else {
				noticeWarning.innerHTML = "<h2>Something went wrong</h2><p>" + data.text + "</p>";
				noticeWarning.classList.remove("hidden");
				window.scrollTo(0, 0);
				_enableSubmitBtn();
			}
		})
		.catch((error) => {
			noticeWarning.innerHTML = "<h2>Something went wrong</h2><p>" + error + "</p>";
			noticeWarning.classList.remove("hidden");
			window.scrollTo(0, 0);
			_enableSubmitBtn();
		});
	};

	var _activateForm = function() {
		_hideGDPRNotice();
		_revealForm();
		_loadReCAPTCHA();
		_setFormFieldEventListeners();
		_setSubmitBtnEventListener()
	};

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



