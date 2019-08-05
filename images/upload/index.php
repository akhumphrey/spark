<?php

/*

	Name: /images/upload/index.php
	Purpose: Provides display and input-testing layers to the image upload process
	Revision: 1

*/

	//	Include Sentry object for session testing
	require_once('../../spark/includes/Sentry.php');
	$sentry = new Sentry();

	//	Record where the user was heading
	if (isset($_SERVER['REQUEST_URI'])){

		$_SESSION['target'] = $_SERVER['REQUEST_URI'];

	}

	if (!$sentry->checkLogin()){

		$_SESSION['event'] = 'notLogged';
		header("Location: /");

	}

	//	Unset the page target since they should have successfully logged in now
	unset($_SESSION['target']);

	// Include Validator object for testing	require_once('../../spark/includes/Validator.php');
	$validateEvent = new Validator();

	//	Filename for uploaded image
	$filename = '';

	//	Target for uploaded images
	$targetDir = '/var/www/spark/gallery';

	//	If the form has been submitted with a file which is larger than zero
	if(isset($_FILES['uploadPath']) && $_FILES['uploadPath']['size'] > 0) {

			//	Include the Image object to manipulate the upload
			include('../../spark/includes/Image.php');

			//	Generate unique string for filenames
			$hash = md5(time());
			$extension	= strtolower(strrchr($_FILES['uploadPath']['name'],"."));
			$unique = $hash . $extension;

			// Insert the file extension into $_SESSION for later use
			$_SESSION['extension'] = $extension;

			//	Beging processing 100x100px thumbnail
			$small = new Image($_FILES['uploadPath'], $unique);

			//	Insert image dimensions and filesize into $_SESSION for later use
			$_SESSION['dimensions'] = $small->getSize();
			$_SESSION['filesize'] = $_FILES['uploadPath']['size'];

			//	Continue processing 100x100 thumbnail
			$small -> setTargetdir($targetDir);
			$small -> square('center');
			$small -> resize(100, 100, 'fit');
			$small -> convert('jpg');
			$filename = $small -> save('sm_');

			//	Copy original image to target dir & take note in $_SESSION for later use
			$_SESSION['largeFile'] = $small->largeImage();
			$_SESSION['largePath'] = '/spark/gallery/' . $_SESSION['largeFile'];

			//	Insert 100x100 details into $_SESSION for later use
			$_SESSION['smallFile'] = $filename;
			$_SESSION['smallPath'] = '/spark/gallery/' . $filename;

			//	Process 350x350px thumbnail
			$med = new Image($_FILES['uploadPath'], $unique);
			$med -> setTargetDir($targetDir);
			$med -> square('center');
			$med -> resize(350, 350, 'fit');
			$med -> convert('jpg');
			$filename = $med -> save('med_');

			//	Clean up the generated temp files
			$med -> cleanup();
			$small -> cleanup();

			//	Destroy the $small and $medium objects
			unset($med);
			unset($small);

			//	Insert the 350x350 details into $_SESSION for later use
			$_SESSION['mediumFile'] = $filename;
			$_SESSION['mediumPath'] = '/spark/gallery/' . $filename;

			//	This is a fresh upload, not an old image being viewed
			$_SESSION['event'] = 'new';

			//	Go to the details page for further editing
			header("Location: /images/details/");

	}
	else {

		// Test for previously-set event, and confirm it's not injected code
		if (((isset($_POST['event'])) && ($validateEvent->strictText($_POST['event']))) || ((isset($_SESSION['event'])) && ($validateEvent->strictText($_SESSION['event'])))){

			//	Not injected code, use $_POST if possible, otherwise use $_SESSION
			if (isset($_POST['event'])){

				$event = $_POST['event'];

			}
			else {

				$event = $_SESSION['event'];

			}

			//	Test for which event we're tracking
			switch ($event){

				case 'error':

					$message = '<div id="uploadError">' . '<div id="uploadErrorImg"><img src="/spark/images/errorCross.png" alt="error" /></div>' . '<div id="uploadErrorTxt">There was a problem with the image.<br />Please try again.</div>' . '</div>';
					break;

			}
		}
	}

	//	Include Header object to ouput basic header information
	require_once('../../spark/includes/Header.php');
	$header = new Header();

?>
<style type="text/css">
	<!--
		@import url("/spark/styles/header.css");
		@import url("/spark/styles/upload.css");
	-->
</style>

<script type="text/javascript" src="/spark/scripts/details.js"></script>

<title>spark.upload</title>
</head>
<body>

<?php

	//	Generate navigation tabs required for this page
	$header->tabs('images');

?>

<div id="bodyWrapper">
	<div id="uploadImage">
		<img src="/spark/images/upload.png" alt="upload" />
	</div>
	<div>
		<?php if (isset($message)){ echo $message; } ?>
	</div>
	<div id="uploadForm">
		<form action="/images/upload/" method="post" enctype="multipart/form-data" onsubmit="disButtons();">
			<div id="formPath"><input name="uploadPath" type="file" onchange="enButton();" /></div>
			<div id="formButtons"><input id="formSubmit" name="uploadSubmit" type="submit" value="add image" disabled="disabled" /> <input id="formCancel" name="uploadCancel" type="button" value="cancel" onclick="returnHome();" /></div>
		</form>
	</div>
</div>
<?php

	//	Generate footer and copyright information
	require_once('../../spark/includes/Footer.php');
	$footer = new Footer();

?>
</body>
</html>