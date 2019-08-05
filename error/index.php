<?php

/*

	Name: /error/index.php
	Purpose: Static 404 error page
	Revision: 1

*/

	//	Include Header object to ouput basic header information
	require_once('../spark/includes/Header.php');
	$header = new Header();

?>
<style type="text/css">
	<!--
		@import url("/spark/styles/error.css");
	-->
</style>

<title>spark.error</title>
</head>
<body>
<div id="errorWrapper">
	<div id="errorHeader">
		<img src="/spark/images/loginSpark.png" alt="spark.error" /><img src="/spark/images/loginExclaim.png" alt="spark.error" /><img src="/spark/images/loginError.png" alt="spark.error" />
	</div>
	<div id="error">
		<div id="errorImg"><img src="/spark/images/errorCross.png" alt="error" /></div>
		<div id="errorTxt"><strong>Opps!</strong><br />Something appears to have gone wrong with the page you requested.<br />Either you don't have access to the page (because you're not logged in), or the page simply isn't there (a typo possibly?).<br /><br />There's a number of things you could try from here: <a href="/" title="log in">log in</a>, <a href="javascript:history.back();" title="return to where you came from">return to where you came from</a>, or try typing the url again.</div>
	</div>
</div>
<?php

	//	Generate footer and copyright information
	require_once('../spark/includes/Footer.php');
	$footer = new Footer();

?>
</body>
</html>