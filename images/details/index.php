<?php

/*

	Name: /images/details/index.php
	Purpose: Provides editing, viewing, downloading and deletion facilities for user-uploaded images
	Revision: 1

*/

	//	Include Sentry object for futher user testing
	require_once('../../spark/includes/Sentry.php');
	$sentry = new Sentry();

	//	Record where the user was heading
	if (isset($_SERVER['REQUEST_URI'])){

		$_SESSION['target'] = $_SERVER['REQUEST_URI'];

	}

	//	If the user details fail to log in
	if (!$sentry->checkLogin()){

		//	Return to the login page with a 'not logged in' error message
		$_SESSION['event'] = 'notLogged';
		header("Location: /");

	}

	//	Unset the page target since they should have successfully logged in now
	unset($_SESSION['target']);

	// Include Validator object for testing of event
	require_once('../../spark/includes/Validator.php');
	$validator = new Validator();

	//	Is user browsing to an existing image
	if (isset($_GET['iD'])){

		//	Is the requested image a valid index
		if ($validator->validInteger((int) $_GET['iD'])){

			//	Store the requested image iD
			$requestedImage = $_GET['iD'];

			// Include DbConnector object to query the database
			require_once('../../spark/includes/DbConnector.php');
			$connector = new DbConnector();

			//	Generate a query for the requested image iD
			$query = 'SELECT * FROM `' . $connector->dbTables['images'] . '` WHERE `iD`=' . $requestedImage;

			//	If the database query succeeded
			if ($result = $connector->query($query)){

				//	Count the number of returned rows
				$count = $connector->getNumRows($result);

				//	If the image iD exists
				if ($count > 0){

					//	Generate an image object for the matched iD
					$image = $connector->fetchObject($result);

					//	If the user wants to delete the image
					if ((isset($_GET['event'])) && ($_GET['event'] == "delete")) {

						$_SESSION['event'] = 'delete';

					}

					//	Saving changes to an existing image
					else if ((isset($_GET['event'])) && ($_GET['event'] == "save")) {

						$_SESSION['event'] = 'save';

					}

					//	Success event coming from save to existing image
					else if ((isset($_SESSION['event'])) && ($_SESSION['event'] == "success")) {

						//	Do nothing, just catching the event before the else{}

					}
					else {

						//	The user justs want to edit
						$_SESSION['mediumPath'] = $image->pathM;
						$_SESSION['event'] = 'edit';

					}
				}
				else{

					//	Set the correct event for reporting no image found
					$_SESSION['event'] = 'notFound';

				}
			}
			else {

				//	The query failed to execute
				die('query failed');

			}
		}
		else {

			//	Theoretically this event should never occur, the regex in the .htaccess should disallow non-integer get parameters.
			//	Successfully reaching this point implies backdoor / hacking tactics
			die('invalid image');

		}
	}

	//	Do we have an event set?
	if (isset($_POST['event']) || isset($_SESSION['event'])){

		//	If the event is a $_POST and is valid
		if ((isset($_POST['event'])) && ($validator->strictText($_POST['event']))) {

			$event = $_POST['event'];

		}

		//	If the event is a $_SESSION and is valid
		else if ((isset($_SESSION['event'])) && ($validator->strictText($_SESSION['event']))) {

			$event = $_SESSION['event'];
			unset($_SESSION['event']);

		}

		//	Test for which event we're tracking
		switch ($event){

			//	There was an error with the uploaded image - most likely it wasn't an image
			case 'error':

				$_SESSION['event'] = 'error';
				header("Location: /images/upload/");
				break;

			//	The user wants to edit an existing image
			case 'edit':

				//	Include File object for file information
				require_once('../../spark/includes/File.php');
				$file = new File();

				$message = '<form action="/images/details/" method="post">' . "\n\t\t\t";
				$message .= '<p><input id="detDownload" name="detailDownload" type="button" value="download" onclick="download(\'' . $image->pathL . '\');" /></p><br />' . "\n\t\t\t";
				$message .= '<ul id="detailsWrapper2">' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detNameLeft">name:</p>' . "\n\t\t\t\t\t";
				
				if (($_SESSION['group'] == 0) || ($image->owner == $_SESSION['user'])){

					$message .= '<p id="detNameRight"><input name="detailsName" type="text" value="' . $image->name . '" /></p>' . "\n\t\t\t\t";

				}
				else {
				
					$message .= '<p id="detNameRight"><span>' . $image->name . '</span></p>' . "\n\t\t\t\t";
					
				}
				
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detOwnerLeft">owner:</p>' . "\n\t\t\t\t\t";
				
				if ($_SESSION['group'] == 0){
				
					$message .= '<p id="detOwnerRight"><input name="detailsOwner" type="text" value="' . $image->owner . '" /></p>' . "\n\t\t\t\t";
					
				}
				else {
				
					$message .= '<p id="detOwnerRight">' . $image->owner . '</p>' . "\n\t\t\t\t";
				
				}
				
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detDateLeft">date:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detDateRight">' . date('g:ia, d/m/Y', (int)$image->timeStamp) . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detFormatLeft">format:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detFormatRight">' . $image->format . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detWidthLeft">width:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detWidthRight">' . $image->width . 'px</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detHeightLeft">height:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detHeightRight">' . $image->height . 'px</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detSizeLeft">filesize:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detSizeRight">' . $file->getFileSize($image->size) . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detCatLeft">category:</p>' . "\n\t\t\t\t\t";

				if (($_SESSION['group'] == 0) || ($image->owner == $_SESSION['user'])){

					$message .= '<p id="detCatRight">' . "\n\t\t\t\t\t\t";
					$message .= '<select name="detailsCat">' . "\n\t\t\t\t\t\t\t";
				
					if ($image->category == 'misc'){

						$message .= '<option value="wallpapers">wallpapers</option>' . "\n\t\t\t\t\t\t\t";
						$message .= '<option value="misc" selected="selected">misc</option>' . "\n\t\t\t\t\t\t";

					}
					else {

						$message .= '<option value="wallpapers" selected="selected">wallpapers</option>' . "\n\t\t\t\t\t\t\t";
						$message .= '<option value="misc">misc</option>' . "\n\t\t\t\t\t\t";

					}

					$message .= '</select>' . "\n\t\t\t\t\t";

				}
				else {

					$message .= '<p id="detCatRight">';
					$message .= '<span>' . $image->category . '</span>';

				}

				$message .= '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detKeysLeft">keywords:</p>' . "\n\t\t\t\t\t";

				if (($_SESSION['group'] == 0) || ($image->owner == $_SESSION['user'])){

					$message .= '<p id="detKeysRight"><input name="detailsKeys" type="text" value="' . $image->keywords . '" /></p>' . "\n\t\t\t\t";

				}
				else {

					$message .= '<p id="detKeysRight"><span>' . $image->keywords . '</span></p>' . "\n\t\t\t\t";

				}

				$message .= '</li>' . "\n\t\t\t\t";

				if (($_SESSION['group'] == 0) || ($image->owner == $_SESSION['user'])){

					$message .= '<li>' . "\n\t\t\t\t\t";
					$message .= '<input name="event" type="hidden" />' . "\n\t\t\t\t";
					$message .= '</li>' . "\n\t\t\t\t";
					$message .= '<li>' . "\n\t\t\t\t\t";
					$message .= '<p id="detButLeft"><input id="detButtonConfirm" name="detailConfirm" type="button" value="save" onclick="saveDetails(' . $image->iD . ');" /></p>' . "\n\t\t\t\t\t";
					$message .= '<p id="detButRight"><input id="detButtonCancel" name="detailCancel" type="button" value="cancel" onclick="returnHome();" />' . "\n\t\t\t\t\t";
					$message .= '<p id="delButMid"><input id="detButtonDelete" name="detailDelete" type="button" value="delete" onclick="delImage(' . $image->iD . ');" /></p>' . "\n\t\t\t\t";
					$message .= '</li>' . "\n\t\t\t";

				}
				else {

					$message .= '<li>' . "\n\t\t\t\t\t";
					$message .= '<p id="detButMid"><input id="detButtonReturn" name="successReturn" type="button" value="return to gallery" onclick="returnHome();" /></p>' . "\n\t\t\t\t";
					$message .= '</li>' . "\n\t\t\t";

				}
				$message .= '</ul>' . "\n\t\t";
				$message .= '</form>' . "\n";
				break;

			//	The image index wasn't found
			case 'notFound':

				unset($_SESSION['mediumPath']);
				$message = '<p id="detNotFound">That image does not exist.</p>';
				$message .= '<p id="detNotFoundBut"><input name="detailCancel" type="button" value="return to gallery" onclick="returnHome();" />' . "\n\t\t\t\t\t";
				break;

			//	The user wants to delete an existing image
			case 'delete':

				//	Nuke appropriate images
				if ((unlink('/var/www/' . $image->pathL)) && (unlink('/var/www/' . $image->pathM)) && (unlink('/var/www/' . $image->pathS))){

					//	Prepare query to delete database entry
					$query = 'DELETE FROM `' . $connector->dbTables['images'] . '` WHERE `iD`=' . $image->iD . ' LIMIT 1';

					//	If database query was successful
					if ($result = $connector->query($query)){

						//	Unset all image-related $_SESSION variables
						unset($_SESSION['event']);
						unset($_SESSION['largePath']);
						unset($_SESSION['mediumPath']);
						unset($_SESSION['smallPath']);
						unset($_SESSION['extension']);
						unset($_SESSION['filesize']);
						unset($_SESSION['dimensions']);

						//	Output a success message
						$message = '<ul id="detailsMessage">' . "\n\t\t\t";
						$message .= '<li>' . "\n\t\t\t\t";
						$message .= '<p id="sucImg"><img src="/spark/images/successTick.png" alt="" /></p>' . "\n\t\t\t\t";
						$message .= '<p id="sucTxt">file <strong>' . stripslashes(trim($image->name)) . '</strong> deleted successfully.</p>' . "\n\t\t\t";
						$message .= '</li>' . "\n\t\t\t";
						$message .= '<li>' . "\n\t\t\t\t";
						$message .= '<p id="sucButLeft"><input name="successReturn" type="button" value="return to gallery" onclick="returnHome();" /></p>' . "\n\t\t\t\t";
						$message .= '<p id="sucButRight"><input name="successAdd" type="button" value="add another image" onclick="addAnother();" /></p>' . "\n\t\t\t";
						$message .= '</li>' . "\n\t\t";
						$message .= '</ul>';

						//	Destroy the image object
						unset($image);

					}
				}
				break;

			//	The image is newly uploaded and hasn't been inserted into the database yet
			case 'new':

				//	Include File object for file information
				require_once('../../spark/includes/File.php');
				$file = new File();

				$message = '<form action="/images/details/" method="post">' . "\n\t\t\t";
				$message .= '<ul id="detailsWrapper">' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detNameLeft">name:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detNameRight"><input name="detailsName" type="text" /></p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detOwnerLeft">owner:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detOwnerRight">' . $_SESSION['user'] . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detFormatLeft">format:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detFormatRight">' . $_SESSION['extension'] . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detWidthLeft">width:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detWidthRight">' . $_SESSION['dimensions'][0] . 'px</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detHeightLeft">height:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detHeightRight">' . $_SESSION['dimensions'][1] . 'px</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detSizeLeft">filesize:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detSizeRight">' . $file->getFileSize($_SESSION['filesize']) . '</p>' . "\n\t\t\t\t";

				//	Destroy the $file object as it's no longer needed
				unset($file);

				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detCatLeft">category:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detCatRight">' . "\n\t\t\t\t\t\t";
				$message .= '<select name="detailsCat">' . "\n\t\t\t\t\t\t\t";
				$message .= '<option value="wallpapers">wallpapers</option>' . "\n\t\t\t\t\t\t\t";
				$message .= '<option value="misc">misc</option>' . "\n\t\t\t\t\t\t";
				$message .= '</select>' . "\n\t\t\t\t\t";
				$message .= '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detKeysLeft">keywords:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detKeysRight"><input name="detailsKeys" type="text" /></p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<input name="event" type="hidden" />' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detButLeft"><input name="detailConfirm" type="button" value="confirm" onclick="checkDetails();" /></p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detButRight"><input name="detailCancel" type="button" value="cancel" onclick="cancelDetails();" /></p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t";
				$message .= '</ul>' . "\n\t\t";
				$message .= '</form>' . "\n";
				break;

			//	The details for a new image have been entered, now insert them into the db
			case 'details':

				// Include DbConnector object to query the database
				require_once('../../spark/includes/DbConnector.php');
				$connector = new DbConnector();

				// If the new data is valid, continue
				if (($validator->looseText($_POST['detailsName'])) && ($validator->strictText($_POST['detailsCat'])) && ($validator->looseText($_POST['detailsKeys']))){

					//	Define insert statement
					$details = "INSERT INTO `" . $connector->dbTables['images'] . "` (name,owner,pathL,pathM,pathS,format,category,size,width,height,keywords,timeStamp) VALUES ('" . addslashes($_POST['detailsName']) . "','" . addslashes($_SESSION['user']) . "','" . addslashes($_SESSION['largePath']) . "','" . addslashes($_SESSION['mediumPath']) . "','" . addslashes($_SESSION['smallPath']) . "','" . addslashes($_SESSION['extension']) . "','" . addslashes($_POST['detailsCat']) . "','" . addslashes($_SESSION['filesize']) . "','" . addslashes($_SESSION['dimensions'][0]) . "','" . addslashes($_SESSION['dimensions'][1]) . "','" . addslashes($_POST['detailsKeys']) . "','" . time() . "')";

					//	If the insert was successful
					if ($result = $connector->query($details)){

						$_SESSION['event'] = 'success';
						$_SESSION['imageName'] = $_POST['detailsName'];

					}
					else {

						//	Something went wrong with the db insertion
						//$_SESSION['event'] = 'error';
						die('error inserting');

					}
				}
				else {

					//	Invalid data
					$_SESSION['event'] = 'badDetails';

				}

				//	Reprocesses the page
				header("Location: /images/details/");
				break;

			//	The user is saving changes to an existing image
			case 'save':

				// Include DbConnector object to query the database
				require_once('../../spark/includes/DbConnector.php');
				$connector = new DbConnector();

				// If the new data is valid, continue
				if (($validator->looseText($_POST['detailsName'])) && ($validator->strictText($_POST['detailsCat'])) && ($validator->looseText($_POST['detailsKeys']))){

					if ((isset($_POST['detailsOwner'])) && ($validator->strictText($_POST['detailsOwner']))){

						$details = "UPDATE `" . $connector->dbTables['images'] . "` SET name='" . addslashes($_POST['detailsName']) . "',owner='" . addslashes($_POST['detailsOwner']) . "',category='" . addslashes($_POST['detailsCat']) . "',keywords='" . addslashes($_POST['detailsKeys']) . "' WHERE `iD`=" . $image->iD;

					}
					else {

						//	Define update statement
						$details = "UPDATE `" . $connector->dbTables['images'] . "` SET name='" . addslashes($_POST['detailsName']) . "',category='" . addslashes($_POST['detailsCat']) . "',keywords='" . addslashes($_POST['detailsKeys']) . "' WHERE `iD`=" . $image->iD;

					}

					//	If the update statement succeeded
					if ($result = $connector->query($details)){

						//	Set the mediumPath variable for display further down the page
						$_SESSION['mediumPath'] = $image->pathM;
						$_SESSION['event'] = 'success';

					}
					else {

						//	Something went wrong with the db insertion
						//$_SESSION['event'] = 'error';
						die('error saving');

					}
				}
				else {

					//	Invalid data
					$_SESSION['event'] = 'badDetails';

				}

				//	Reprocesses the page
				header("Location: /images/details/" . $image->iD . "/");
				break;


			//	The user entered badly-formatted or injected text, give them an error and the chance to resubmit
			case 'badDetails':

				//	Include File object for file information
				require_once('../../spark/includes/File.php');
				$file = new File();

				$message = '<div id="uploadError">' . '<div id="uploadErrorImg"><img src="/spark/images/errorCross.png" alt="error" /></div>' . '<div id="uploadErrorTxt">The details you entered were invalid.<br />Please try again.</div>' . '</div><br />';
				$message .= '<form action="/images/details/" method="post">' . "\n\t\t\t";
				$message .= '<ul id="detailsWrapper">' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detNameLeft">name:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detNameRight"><input name="detailsName" type="text" /></p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detOwnerLeft">owner:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detOwnerRight">' . $_SESSION['user'] . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detFormatLeft">format:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detFormatRight">' . $_SESSION['extension'] . '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detWidthLeft">width:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detWidthRight">' . $_SESSION['dimensions'][0] . 'px</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detHeightLeft">height:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detHeightRight">' . $_SESSION['dimensions'][1] . 'px</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detSizeLeft">filesize:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detSizeRight">' . $file->getFileSize($_SESSION['filesize']) . '</p>' . "\n\t\t\t\t";

				//	Destroy the $file object as it's no longer needed
				unset($file);

				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detCatLeft">category:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detCatRight">' . "\n\t\t\t\t\t\t";
				$message .= '<select name="detailsCat">' . "\n\t\t\t\t\t\t\t";
				$message .= '<option value="wallpapers">wallpapers</option>' . "\n\t\t\t\t\t\t\t";
				$message .= '<option value="misc">misc</option>' . "\n\t\t\t\t\t\t";
				$message .= '</select>' . "\n\t\t\t\t\t";
				$message .= '</p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detKeysLeft">keywords:</p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detKeysRight"><input name="detailsKeys" type="text" /></p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<input name="event" type="hidden" />' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t\t";
				$message .= '<p id="detButLeft"><input name="detailConfirm" type="button" value="confirm" onclick="checkDetails();" /></p>' . "\n\t\t\t\t\t";
				$message .= '<p id="detButRight"><input name="detailCancel" type="button" value="cancel" onclick="cancelDetails();" /></p>' . "\n\t\t\t\t";
				$message .= '</li>' . "\n\t\t\t";
				$message .= '</ul>' . "\n\t\t";
				$message .= '</form>' . "\n";
				break;

			//	The upload and db insertion went ok
			case 'success':

				unset($_SESSION['event']);
				$message = '<ul id="detailsMessage">' . "\n\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t";
				$message .= '<p id="sucImg"><img src="/spark/images/successTick.png" alt="" /></p>' . "\n\t\t\t\t";

				if (isset($image->name)){

					$message .= '<p id="sucTxt">file <strong>' . stripslashes(trim($image->name)) . '</strong> edited successfully.</p>' . "\n\t\t\t";

				}
				else {

					$message .= '<p id="sucTxt">file <strong>' . stripslashes(trim($_SESSION['imageName'])) . '</strong> uploaded successfully.</p>' . "\n\t\t\t";
					unset ($_SESSION['imageName']);

				}

				$message .= '</li>' . "\n\t\t\t";
				$message .= '<li>' . "\n\t\t\t\t";
				$message .= '<p id="sucButLeft"><input name="successReturn" type="button" value="return to gallery" onclick="returnHome();" /></p>' . "\n\t\t\t\t";
				$message .= '<p id="sucButRight"><input name="successAdd" type="button" value="add another image" onclick="addAnother();" /></p>' . "\n\t\t\t";
				$message .= '</li>' . "\n\t\t";
				$message .= '</ul>';
				break;

			//	User has aborted upload / details entry
			case 'cancel':

				//	Nuke the appropriate images
				if ((unlink('/var/www/' . $_SESSION['largePath'])) && (unlink('/var/www/' . $_SESSION['mediumPath'])) && (unlink('/var/www/' . $_SESSION['smallPath']))){

					//	Unset any relevant session variables
					unset($_SESSION['largePath']);
					unset($_SESSION['mediumPath']);
					unset($_SESSION['smallPath']);
					unset($_SESSION['extension']);
					unset($_SESSION['filesize']);
					unset($_SESSION['dimensions']);

					//	Return to the gallery
					unset($_SESSION['event']);
					header("Location: /images/");

				}

				break;

			//	Echo $event to see what happened - theoretically, this event should never occur
			default:
				die('"' . $event . '"');
				break;

		}
	}
	else {

		header("Location: /images/");

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

<title>spark.details</title>
</head>
<body>
<?php

	//	Generate navigation tabs required for this page
	$header->tabs('images');

?>
<div id="bodyWrapper">
	<div id="imageWrapper">
		<span><?php

			//	This will need to be fixed right now - it's still only works with an uploaded file
			if (isset($_SESSION['mediumPath'])){

				//	Output the uploaded file
				echo '<img src="' . $_SESSION['mediumPath'] . '" alt="" class="image" /></span>' . "\n";

			}
			else {

				echo '<img src="/spark/images/upload.png" alt="image not found" /></span>' . "\n";

			}
		?>
	</div>
	<div id="message">
		<?php echo $message; ?>
	</div>
</div>
<?php

	//	Generate footer and copyright information	
	require_once('../../spark/includes/Footer.php');
	$footer = new Footer();

?>
</body>
</html>