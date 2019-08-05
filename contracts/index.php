<?php

/*

	Name: /contracts/index.php
	Purpose: Provides add / update / delete / search facilities for contracts administration
	Revision: 1

*/

	//	Include Sentry object for session testing
	require_once('../spark/includes/Sentry.php');
	$sentry = new Sentry();

	//	Record where the user was heading
	if (isset($_SERVER['REQUEST_URI'])){

		$_SESSION['target'] = $_SERVER['REQUEST_URI'];

	}

	//	If the user details fail to log in
	if (!$sentry->checkLogin()){

		//	Return to the login page with a login error message
		$_SESSION['event'] = 'notLogged';
		header("Location: /");

	}

	//	Output a suitable access denied page
	$message = '<div id="contractMessage">' . "\n\t";
	$message .= '<p id="contractMessageImg"><img src="/spark/images/errorCross.png" alt="error" /></p>' . "\n\t";
	$message .= '<p id="contractMessageTxt">You do not have access to view this page.<br /></p>' . "\n\t";
	$message .= '</div>' . "\n";
	
	//	Include Header object to ouput basic header information
	require_once('../spark/includes/Header.php');
	$header = new Header();

?>
<style type="text/css">
	<!--
		@import url("/spark/styles/header.css");
		@import url("/spark/styles/contracts.css");		
	-->
</style>

<title>spark.contracts</title>
</head>
<body>
<?php

	//	Generate navigation tabs required for this page
	$header->tabs('contracts');

	//	Begin output
	echo '<div id="bodyWrapper">';

	//	Output the page
	echo $message;

	//	Close off the bodyWrapper
   echo '</div>' . "\n";

	//	Generate footer and copyright information	
	require_once('../spark/includes/Footer.php');
	$footer = new Footer();

?>
</body>
</html>