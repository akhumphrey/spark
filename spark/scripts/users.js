/*
		NAME: users.js
		PURPOSE: Provide javascript functions to user administration facilities
		AFFECTS: /users/index.php
		REVISION: 1
*/

function delUser(iD){
	var conf = confirm('Are you sure you?');
	if (conf){
		dest = "/users/" + iD + "/delete/";
		document.location.href = dest;
	}
}

function addUser(){
	dest = "/users/new/";
	document.location.href = dest;
}

function save(iD){
	var errorArray = new Array;
	var txtFormat = /^[a-zA-Z]+/;
	var passFormat = /^[a-zA-Z0-9]{8,}/;
	var phFormat = /^\+[0-9]{2}\s[0-9]{1,2}\s[0-9]{3}\s[0-9]{4}$/;
	var i=0;
	var result = "";
	var object = document.forms[0];
	
	if (object.userName != null){
		if (!txtFormat.test(object.userName.value)){
			errorArray[i] = "the username";
			i++;
		}
	}

	if ((object.password1 != null) || (object.password2 != null)){
		if ((!passFormat.test(object.password1.value)) || (!passFormat.test(object.password2.value)) || (object.password1.value != object.password2.value)){
			errorArray[i] = "the passwords";
			i++;
		}
	}

	if (!txtFormat.test(object.firstName.value)){
		errorArray[i] = "the user's first name";
		i++;
	}

	if (!txtFormat.test(object.lastName.value)){
		errorArray[i] = "the user's last name";
		i++;
	}

	if (object.phoneNumber.value != ''){
		if (!phFormat.test(object.phoneNumber.value)){
			errorArray[i] = "the user's phone number";
		}
	}

	if (errorArray.length > 0){
		for (var i=0; i < errorArray.length; i++){
			if ((i == errorArray.length - 1) & (errorArray.length > 1)){
				result += " and " + errorArray[i];
			}
			else if (errorArray.length > 1){
				result += errorArray[i] + ", ";
			}
			else {
				result += errorArray[i];
			}
		}
		alert("please check " + result + ".");
		return false;
	}
	else {
		if (iD == null){
			object.action = "/users/save/";
			object.submit();
		}
		else {
			object.saveBut.disabled = true;
			object.cancelBut.disabled = true;
			object.action = "/users/" + iD + "/save/";
			object.submit();
		}
	}
}

function cancel(){
	dest = "/users/";
	document.location.href = dest;
}