<?php

/*

	Name: /index.php
	Purpose: Provides display and input-testing layers to the login / logout process
	Revision: 1

*/

	//	Include Sentry object for futher user testing
	require_once('spark/includes/Sentry.php');
	$sentry = new Sentry();

	//	If the user requested another page and was redirected here to login first
	if (isset($_SESSION['target'])){

		//	Store the target for redirection after sucessful login
		$target = $_SESSION['target'];
		unset($_SESSION['target']);

	}

	// Include Validator object for testing of event
	require_once('spark/includes/Validator.php');
	$validateEvent = new Validator();

	// Test for previously-set event, and confirm it's not injected code
	if (((isset($_POST['event'])) && ($validateEvent->strictText($_POST['event']))) || ((isset($_SESSION['event'])) && ($validateEvent->strictText($_SESSION['event'])))){

		//	Not injected code, use $_POST if possible, otherwise use $_SESSION
		if (isset($_POST['event'])){

			$event = $_POST['event'];

		}
		else {

			$event = $_SESSION['event'];
			unset($_SESSION['event']);

		}

		//	Test for which event we're tracking
		switch ($event){

			// User wants to log in
			case 'login':

			//	Check login data
			if ($sentry->checkLogin($_POST['user'],$_POST['pass'])){

					//	If the user wanted a page other than /images/
					if (isset($target)){

						unset($_SESSION['target']);
						header("Location: " . $target);

					}

					//	They wanted /images/
					else {

						unset($_SESSION['target']);
						header("Location: /images/");

					}
				}

				//	Login failed, reload with error message
				else {

					$_SESSION['event'] = 'error';
					header("Location: /");

				}

				break;

			// User wants to log out
			case 'logout':

				if ($sentry->logout()){

					$message = '<div id="loginMessage">' . "\n\t\t";
					$message .= '<div id="loginMessageImg"><img src="/spark/images/successTick.png" alt="success" /></div>' . "\n\t\t";
					$message .= '<div id="loginMessageTxt">You have been successfully logged out.</div>' . "\n\t";
					$message .= '</div>' . "\n\t";

				}

				break;

			//	There was an error with the user's data - most likely bad username/password
			case 'error':

				$message = '<div id="loginMessage">' . "\n\t";
				$message .= '<div id="loginMessageImg"><img src="/spark/images/errorCross.png" alt="error" /></div>' . "\n\t";
				$message .= '<div id="loginMessageTxt">Your username or password were incorrect.<br />Please try again.</div>' . "\n\t";
				$message .= '</div>' . "\n";

				break;

			//	The user has requested a valid page but isn't logged in
			case 'notLogged':

				$message = '<div id="loginMessage">' . "\n\t";
				$message .= '<div id="loginMessageImg"><img src="/spark/images/errorCross.png" alt="error" /></div>' . "\n\t";
				$message .= '<div id="loginMessageTxt">You must log in to view that page.<br />Please try again.</div>' . "\n\t";
				$message .= '</div>' . "\n";

				break;

			//	Die with $event to see what happened - theoretically, this event should never occur
			default:

				die('Error: ' . $event);

				break;

		}
	}

	//	Include Header object to ouput basic header information
	require_once('spark/includes/Header.php');
	$header = new Header();

?>
<style type="text/css">
	<!--
		@import url("/spark/styles/login.css");
	-->
</style>

<script type="text/javascript" src="/spark/scripts/login.js"></script>

<?php if ((isset($event)) && ($event == 'error')){ echo '<title>spark!error</title>' . "\n"; } else { echo '<title>spark.login</title>' . "\n"; } ?>
</head>
<body>

<div id="loginWrapper">
	<div id="loginHeader">
		<?php
			// If the event is an error, output the error header
			if ((isset($event)) && (($event == 'error') || ($event == 'notLogged'))){

				echo '<img src="/spark/images/loginSpark.png" alt="spark.error" /><img src="/spark/images/loginExclaim.png" alt="spark.error" /><img src="/spark/images/loginError.png" alt="spark.error" />' . "\n";

			}

			//	Otherwise just output the normal header
			else {

				echo '<img src="/spark/images/loginSpark.png" alt="spark.login" /><img src="/spark/images/loginDot.png" alt="spark.login" /><img src="/spark/images/loginLogin.png" alt="spark.login" />' . "\n";

			}
		?>
	</div>
	<?php if (isset($message)){ echo $message; } ?>
<div id="loginForm">
		<form action="/" method="post">
			<p>username:<input name="user" type="text" size="15" tabindex="1" class="loginTxtField" /></p>
			<p>password:<input name="pass" type="password" size="15" tabindex="2" class="loginTxtField" /></p>
			<p><input name="login" type="button" value="login" class="loginBtn" onclick="logIn();" /><input name="reset" type="reset" value="reset" class="loginBtn" /></p>
			<span><input name="event" type="hidden" /></span>
		</form>
	</div>
</div>
</body>
</html>