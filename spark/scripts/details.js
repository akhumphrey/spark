/*
		NAME: details.js
		PURPOSE: Provide javascript functions to the details and upload facilities
		AFFECTS: /images/details/index.php, /images/upload/index.php
		REVISION: 1
*/

function checkDetails(){
	var errorArray = new Array;
	var txtFormat = /^[a-zA-Z]+/;
	var i=0;
	var result = "";
	var object = document.forms[0];
	
	if (!txtFormat.test(object.detailsName.value)){
		errorArray[i] = "the image name";
		i++;
	}
	
	if (!txtFormat.test(object.detailsKeys.value)){
		errorArray[i] = "at least one keyword";
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
		alert("please enter " + result + ".");
		return false;
	}
	else {
		object.detailConfirm.disabled = true;
		object.detailCancel.disabled = true;
		object.event.value = "details";
		object.submit();
	}
}

function disButtons(){
	var object = document.forms[0];
	object.uploadSubmit.disabled = true;
	object.uploadCancel.disabled = true;
}

function enButton(){
	var object = document.forms[0];
	if (object.uploadPath.value != ''){
		object.uploadSubmit.disabled = false;
		object.uploadPath.onmousemove = null;
	}
}

function saveDetails(iD){
	var errorArray = new Array;
	var txtFormat = /^[a-zA-Z]+/;
	var i=0;
	var result = "";
	var object = document.forms[0];
	
	if (!txtFormat.test(object.detailsName.value)){
		errorArray[i] = "the image name";
		i++;
	}
	
	if ((object.detailsOwner != undefined) && (object.detailsOwner.value != null)){
		if (!txtFormat.test(object.detailsOwner.value)){
			errorArray[i] = "the owner";
			i++;
		}
	}		
	
	if (!txtFormat.test(object.detailsKeys.value)){
		errorArray[i] = "at least one keyword";
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
		alert("please enter " + result + ".");
		return false;
	}
	else {
		object.action = "/images/details/" + iD + "/save/";
		object.submit();
	}

}

function cancelDetails(){
	var object = document.forms[0];
	object.event.value = "cancel";
	object.action = "/images/details/";
	object.submit();
}

function addAnother(){
	document.location.href = "/images/upload/";
}

function returnHome(){
	document.location.href = "/images/";
}
function download(target){
	window.open(target,"image","scrollbars=yes, resize=yes");
}
function delImage(iD){
	var conf = confirm('Are you sure you?');
	if (conf){
		dest = "/images/details/" + iD + "/delete/";
		document.location.href = dest;
	}
}