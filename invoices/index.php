<?php

/*

	Name: /invoices/index.php
	Purpose: Provides add / update / delete / search facilities for invoice administration
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
	$message = '<div id="invoiceMessage">' . "\n\t";
	$message .= '<p id="invoiceMessageImg"><img src="/spark/images/errorCross.png" alt="error" /></p>' . "\n\t";
	$message .= '<p id="invoiceMessageTxt">You do not have access to view this page.<br /></p>' . "\n\t";
	$message .= '</div>' . "\n";
	
	//	Include Header object to ouput basic header information
	require_once('../spark/includes/Header.php');
	$header = new Header();

?>
<style type="text/css">
	<!--
		@import url("/spark/styles/header.css");
		@import url("/spark/styles/invoices.css");		
	-->
</style>

<title>spark.invoices</title>
</head>
<body>
<?php

	//	Generate navigation tabs required for this page
	$header->tabs('invoices');

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