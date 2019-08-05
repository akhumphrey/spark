/*
		NAME: login.js
		PURPOSE: Validate user login data before sending
		AFFECTS: /index.php
		REVISION: 1
*/

function logIn(){
	var errorArray = new Array;
	var txtFormat = /^[a-zA-Z]+/;
	var passFormat = /^[a-zA-Z0-9]{8,}/;
	var i=0;
	var result = "";
	var object = document.forms[0];
	
	if (!txtFormat.test(object.user.value)){
		errorArray[i] = "your username";
		i++;
	}
	
	if (!passFormat.test(object.pass.value)){
		errorArray[i] = "your password";
	}
	
	if (errorArray.length > 0){
		for (var i=0; i < errorArray.length; i++){
			if ((i == errorArray.length - 1) & (errorArray.length > 1)){
				result += " and " + errorArray[i];
			}
			else {
				result += errorArray[i];
			}
		}
		alert("please check " + result + ".");
		return false;
	}
	else {
		object.event.value = "login";
		object.submit();
	}
}