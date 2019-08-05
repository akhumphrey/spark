<?php

/*

	Class: SystemComponent
	Purpose: Define basic system variables
	Revision: 1

*/

class SystemComponent {

	protected $settings;


	public function getSettings(){

	//	System variables

		$settings['documentRoot']					= '/var/www/';

	//	Database variables

		$settings['dbhost']							= 'localhost';
		$settings['dbusername']						= 'root';
		$settings['dbpassword']						= '$password!';
		$settings['dbname']							= 'spark';

		$settings['dbtables']['articles']		= 'articles';
		$settings['dbtables']['categories']		= 'categories';		
		$settings['dbtables']['formats']			= 'formats';
		$settings['dbtables']['images']			= 'images';
		$settings['dbtables']['users']			= 'users';
		$settings['dbtables']['userGroups']		= 'userGroups';

		return $settings;

	}
}

?>