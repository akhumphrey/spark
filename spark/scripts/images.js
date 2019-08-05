/*
		NAME: images.js
		PURPOSE: Provide javascript functions for the image gallery
		AFFECTS: /images/index.php
		REVISION: 1
*/

function upload(){
	document.location.href = "/images/upload/";
}

function validate(){
	var object = document.forms[0];
/*	if (object.filterKeywords.value == ""){
		object.filterKeywords.disabled = true;
	}*/
	object.filterApplyBtn.disabled = true;
	object.filterAddBtn.disabled = true;
	object.event.value = 'search';
	object.submit();
}