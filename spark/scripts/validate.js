/*
		NAME: validate.js
		PURPOSE: 
		AFFECTS: 
		REVISION: 1
*/

var valid = new Object();
valid.currency = /\$\d{1,3}(,\d{3})*\.\d{2}/;
valid.time = /^([1-9]|1[0-2]):[0-5]\d$/;
valid.email = /^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/;
valid.phoneNumber = /^\(?\d{3}\)?\s|-\d{3}-\d{4}$/;
valid.ipAddress = /^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/;
valid.date = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
valid.txt = /^[a-zA-Z]+/;
	
function validate() {
	var elementArray = document.forms[0].elements; 
	for (var i=0; i < elementArray.length; i++) {
		with (elementArray[i]) { 
			var v = elementArray[i].validator; 
			if (!v) continue; 
				var thePattern = valid[v]; 
				var gotIt = thePattern.exec(value); 
				if (!gotIt) {
					alert (name + ": failure to match " + v + " to " + value);                  
					elementArray[i].select();
					elementArray[i].focus(); 
					return false;
				}
			}
		}
		return true;
	}
}