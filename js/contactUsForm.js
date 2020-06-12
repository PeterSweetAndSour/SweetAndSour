
// This will recreate the effect on the text input where the label shifts upon the input gaining focus.
document.addEventListener("DOMContentLoaded", function(){
	var msgTextArea = document.querySelector("#msgText");
	var msgTextAreaLabel = msgTextArea.parentNode.querySelector("label");
	msgTextArea.addEventListener("focus", function() {
		msgTextAreaLabel.classList.add("focused");
	});
	msgTextArea.addEventListener("blur", function() {
		if(!msgTextAreaValue) {
			msgTextAreaLabel.classList.remove("focused");
		}
	});
});
