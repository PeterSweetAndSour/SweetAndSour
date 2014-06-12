			//Functions to validate email form.

			//Validate form entries
			function validateForm() {

				//Confirm that a name was entered
				var name = document.forms[0].senderName.value;
				if(name == "") {
					alert("Please enter your name.");
					document.forms[0].senderName.focus();
					return false;
				}

				//Confirm that an email address has been entered.
				var email = document.forms[0].senderEmail.value;
				if(email == "") {
					alert("Please enter your own email address so we can reply.");
					document.forms[0].senderEmail.focus();
					return false;
				}

				//Confirm email address conforms to normal format.
				if(! validateEMail(email)) {
					document.forms[0].senderEmail.focus();
					return false;
				}

				//Confirm that the message is not empty
				var sbj = document.forms[0].msgSubject.value;
				if(sbj == "") {
					alert("Please enter a message subject.");
					document.forms[0].msgSubject.focus();
					return false;
				}

				//Confirm that the message is not empty
				var msg = document.forms[0].msgText.value;
				if(msg == "") {
					alert("There appears to be no message.");
					document.forms[0].msgText.focus();
					return false;
				}

			}

			//Check e-mail address follows normal format.  Does NOT confirm that address is valid.
			function validateEMail(email) {
				invalidChars = " /:;,";

				if(email == "")
					return true;

				for(i=0; i < invalidChars.length; i++)
				{
					badChar = invalidChars.charAt(i);
					if(email.indexOf(badChar, 0) != -1)
					{
						alert("Check e-mail address.  Should not contain " + badChar);
						return false;
					}
				}

				atPos = email.indexOf("@");
				if(atPos == 0)
				{
					alert("Need identifier before the '@' symbol");
					return false;
				}

				if(atPos == -1)
				{
					alert("Need '@' symbol in e-mail address");
					return false;
				}

				dotPos = email.indexOf(".");
				if(dotPos == -1)
				{
					alert("Need '.' symbol in e-mail address followed by 'com', 'org' etc.");
					return false;
				}

				return true;
			}
