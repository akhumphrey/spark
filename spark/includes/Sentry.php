<?php

/*

	Class: Sentry
	Purpoes: Control access to pages
	Revision: 1

*/

class Sentry {

	protected $userdata;


	//	Function: __construct, Purpose: start the session and make sure cache isn't public
	public function __construct(){

		session_start();
		header("Cache-control: private");

	}


	//	Function: logout, Purpose: Log out, destroy session
	public function logout(){

		unset($this->userdata);
		session_destroy();
		return true;

	}


	//	Function: checkLogin, Purpose: Log in and redirect to goodRedirect or badRedirect
	public function checkLogin($user = '',$pass = '',$goodRedirect = '',$badRedirect = '',$goodEvent = '',$badEvent = ''){

		// Include and create DbConnector and Validator objects
		require_once('DbConnector.php');
		require_once('Validator.php');
		$validate = new Validator();
		$loginConnector = new DbConnector();

		// If user is already logged in then check credentials
		if (isset($_SESSION['user']) && isset($_SESSION['pass'])){

			// Validate session data
			if (!$validate->strictText($_SESSION['user'])){

				return false;

			}
			if (!$validate->passText($_SESSION['pass'])){

				return false;

			}

			$getUser = $loginConnector->query("SELECT * FROM `" . $loginConnector->dbTables['users'] . "` WHERE username = '" . $_SESSION['user'] . "' AND password = '" . $_SESSION['pass'] . "' AND enabled = 1");

			if ($loginConnector->getNumRows($getUser) > 0){

				// Existing user ok, continue
				if ($goodRedirect != ''){

					$_SESSION['event'] = $goodEvent;
					header("Location: ".$goodRedirect);

				}

				return true;

			}
			else {

				// Existing user not ok, logout
				$this->logout();
				return false;

			}
		}

		// User isn't logged in, check credentials
		else {

			// Validate input
			if (!$validate->strictText($user)){

				return false;

			}
			if (!$validate->strictText($pass)){

				return false;

			}

			// Look up user in DB
			$getUser = $loginConnector->query("SELECT * FROM `" . $loginConnector->dbTables['users'] . "` WHERE username = '$user' AND password = PASSWORD('$pass') AND enabled = 1");
			$this->userdata = $loginConnector->fetchArray($getUser);

			if ($loginConnector->getNumRows($getUser) > 0){

				// Login Fine, store session details
				// Log in
				$_SESSION['useriD']	= $this->userdata['iD'];
				$_SESSION['user']		= $user;
				$_SESSION['pass']		= $this->userdata['password'];
				$_SESSION['group']	=	$this->userdata['group'];

				if ($goodRedirect) {

					$_SESSION['event'] = $goodEvent;
					header("Location: ".$goodRedirect);

				}

				return true;

			}
			else {

				// Login Bad, piss off
				unset($this->userdata);
				if ($badRedirect) {

					$_SESSION['event'] = $badEvent;
					header("Location: ".$badRedirect);

				}

				return false;

			}
		}
	}
}

?>