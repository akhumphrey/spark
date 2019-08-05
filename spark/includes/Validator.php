<?php

/*

	Class: Validator
	Purpose: Provides validation to user-input
	Revision: 1

*/

class Validator {

	//	Create array for errors to be placed
	protected $errors;

	//	Function: validNumber, Purpose: test if input number is valid, Returns: true/false
	public function validNumber($theinput, $description = ''){

		if (is_numeric($theinput)){

			return true;

		}
		else {

			$this->errors[] = $description;
			return false;

		}
	}


	//	Function: validInteger, Purpose: test if input number is valid integer, Returns: true/false
	public function validInteger($theinput, $description = ''){

		if (is_int($theinput)){

			return true;

		}
		else {

			$this->errors[] = $description;
			return false;

		}
	}


	//	Function: validFloat, Purpose: test if input number is valid float, Returns: true/false
	public function validFloat($theinput, $description = ''){

		if (is_float($theinput)){

			return true;

		}
		else {

			$this->errors[] = $description;
			return false;

		}
	}


	//	Function: looseText, Purpose: restrict input text to ascii characters, Returns: true/false
	public function looseText($theinput, $description = ''){

		if (ereg ("^[A-Za-z0-9 \,\.\-_;]+", $theinput )){

			return true;

		}
		else {

			$this->errors[] = $description;
			return false;

		}
	}


	//	Function: strictText, Purpose: restrict input text to alphanumeric characters, Returns: true/false
	public function strictText($theinput, $description = ''){

		if (ereg ("^[A-Za-z0-9]+$", $theinput )){

			return true;

		}
		else {

			$this->errors[] = $description;
			return false;

		}
	}


	// Function: passText, Purpose: restrict input text to MySQL-formatted password hash, Returns: true/false
	public function passText($theinput, $description = ''){

		if (ereg ("^\*[A-Z0-9]+$", $theinput )){

			return true;

		}
		else {

			$this->errors[] = $description;
			return false;

		}
	}

	//	Function: validDate, Purpose: test if input number is valid date, Returns: true/false
	public function validDate($theinput, $description = ''){

		if (strtotime($theinput) === -1 || $theinput == ''){

			$this->errors[] = $description;
			return false;

		}
		else {

			return true;

		}
	}


	//	Function: foundErrors, Purpose: checks to see if any errors have occurred since the object was created, Returns: true/false
	public function foundErrors() {

		if (count($this->errors) > 0){

			return true;

		}
		else{

			return false;

		}
	}


	//	Function: listErrors, Purpose: returns a string list of any found errors seperated by $delim
	public function listErrors($delim = ' '){

		return implode($delim,$this->errors);

	}


	// Function: addError, Purpose: manually add something to the list of errors
	public function addError($description){

		$this->errors[] = $description;

	}
}

?>