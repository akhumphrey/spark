<?php
/*

	Name: /logout/index.php
	Purpose: Redirects user to /index.php with logout event set
	Revision: 1

*/

	require_once('../spark/includes/Sentry.php');
	$sentry = new Sentry();
	$_SESSION['event'] = 'logout';
	header("Location: /");

?>