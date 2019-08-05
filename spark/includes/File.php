<?php

/*

	Class: File
	Purpose: Provide file information functions
	Revision: 1

*/

class File {

	//	Function: getFileSize, Purpose: returns a human-readable filesize string
	public function getFileSize ($bytes, $clean=false, $decimal='.', $spacer=''){

		//	If the required output should be cleaned of decimals or not
		if ($clean){

			//	Cast the passed value as an int before making sure it's positive
			$bytes = max (0, (int)$bytes);

		}
		else {

			//	Make sure the passed value is positive
			$bytes = max(0, $bytes);

		}

		//	Define units of measurement
		$units = array(
						'TB' => 1099511627776,						'GB' => 1073741824,						'MB' => 1048576,						'KB' => 1024,						'B'  => 0
					);
		//	Loop the measurement array's values
		foreach ($units as $unit => $qty){

			//	If the passed value is larger than or equal to the value
			if ($bytes >= $qty){

				//	Seperate the integer and decimal parts of the passed value
				$brokenNumber = explode('.', $bytes);

				//	If there is no decimal value
				if (!$brokenNumber[1]){

					//	Return the formatted value
					return number_format(!$qty ? $bytes: $bytes /= $qty, 0, $decimal,'') . $spacer . $unit;

				}
				else { // There is a decimal value

					//	Return the formatted value (including decimal)
					return number_format(!$qty ? $bytes: $bytes /= $qty, 0, $decimal,'') . $decimal . substr($brokenNumber[1], 0, 2) . $spacer . $unit;

				}
			}
		}
	}
}
?>