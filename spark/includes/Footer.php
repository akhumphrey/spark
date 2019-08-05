<?php

/*

	Class: Footer
	Purpose: Outputs page footer / copyright info
	Revision: 1

*/


class Footer {

	//	Function __construct, Purpose: output the footer
	public function __construct(){

		echo '<div id="copyright">' . "\n\t";
		echo '<p>all user-uploaded and gallery images retain their original copyright, where appropriate.<br /> all other content copyright &copy; 2005 emberDesign, all rights reserved.</p><br />' . "\n";
		echo '</div>' . "\n";

	}
}

?>