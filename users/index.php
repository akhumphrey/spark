<?php

/*

	Name: /users/index.php
	Purpose: Provides add / update / delete facilities for user administration
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

	//	If the user is not an admin
	if ((isset($_SESSION['group'])) && ($_SESSION['group'] > 0)){

		//	Output a suitable access denied page
		$message = '<div id="userMessage">' . "\n\t";
		$message .= '<p id="userMessageImg"><img src="/spark/images/errorCross.png" alt="error" /></p>' . "\n\t";
		$message .= '<p id="userMessageTxt">You do not have access to view this page.<br /></p>' . "\n\t";
		$message .= '</div>' . "\n";

	}
	else {//	User is an admin

		//	Include Validator object for input testing
		require_once('../spark/includes/Validator.php');
		$validator = new Validator();

		//	If the user wants a specific (valid) useriD
		if ((isset($_GET['iD'])) && ($validator->validInteger(intval($_GET['iD'])))){

			//	Store the iD for later use
			$iD = intval($_GET['iD']);

			//	Clear the $_GET variable
			unset($_GET['iD']);

			//	If the user also wants a specific (valid) event
			if ((isset($_GET['event'])) && ($validator->strictText($_GET['event']))){

				//	Store the event for later use
				$event = $_GET['event'];

				//	Clear the $_GET variable
				unset($_GET['event']);

				//	Branch for the user's event
				switch ($event){

					case 'edit'://	User wants to edit an existing user

						// Include DbConnector object to query the database for users
						require_once('../spark/includes/DbConnector.php');
						$connector = new DbConnector();

						//	Prepare query to delete the user
						$query = 'SELECT * FROM `' . $connector->dbTables['users'] . '` WHERE `iD`=' . $iD . ' LIMIT 1';

						//	If the query was successful
						if ($result = $connector->query($query)){
						
							//	Create a user object for outputting
							$user = $connector->fetchObject($result);

							//	Display the form for the user to edit
							$message .= "\n\t" . '<br />' . "\n\t";
							$message .= '<ul id="userDetails">' . "\n\t\t";
							$message .= '<form action="/users/" method="post">' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="idLeft"><p>User iD: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="idRight"><p>' . $user->iD . '</p></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="userLeft"><p>Username: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="userRight"><p>' . $user->username . '</p></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";

//	Password fields - gotta fix the js so these can be added

/*							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="pass1Left"><p>Password: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="pass1Right"><input name="password1" type="password"></span>' . "\n\t\t\t\t";
							$message .= '<span class="userSpacer"><p>8 or more characters</p></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="pass2Left"><p>Repeat Password: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="pass2Right"><input name="password2" type="password"></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
*/							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="nameFLeft"><p>First Name: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="nameFRight"><input name="firstName" type="text" value="' . $user->firstName . '" /></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="nameLLeft"><p>Last Name: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="nameLRight"><input name="lastName" type="text" value="' . $user->lastName . '" /></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="groupLeft"><p>Access Level: </p></span>' . "\n\t\t\t\t";

							//	If the use is the super admin
							if ($user->iD === 1){

								//	Only output the text
								$message .= '<span id="groupRight"><p>admin</p>';

							}
							else {//	Not the super admin

								//	If the user is an admin
								if ($user->group === 0){

									//	Show their access level as admin
									$message .= '<span id="groupRight">' . "\n\t\t\t\t\t";
									$message .= '<select name="group">' . "\n\t\t\t\t\t\t";
									$message .= '<option value="admin" selected="selected">admin</option>' . "\n\t\t\t\t\t\t";
									$message .= '<option value="user">user</option>' . "\n\t\t\t\t\t";
									$message .= '</select>' . "\n\t\t\t\t";

								}
								else {//	Just another plain user

									//	Output regular user access level
									$message .= '<span id="groupRight">' . "\n\t\t\t\t\t";
									$message .= '<select name="group">' . "\n\t\t\t\t\t\t";
									$message .= '<option value="admin">admin</option>' . "\n\t\t\t\t\t\t";
									$message .= '<option value="user" selected="selected">user</option>' . "\n\t\t\t\t\t";
									$message .= '</select>' . "\n\t\t\t\t";

								}
							}

							$message .= '</span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="phoneLeft"><p>Phone Number: </p></span>' . "\n\t\t\t\t";
							$message .= '<span id="phoneRight"><input name="phoneNumber" type="text" value="' . $user->phone . '" /></span>' . "\n\t\t\t\t";
							$message .= '<span class="userSpacer"><p>eg: +64 21 123 4567</p></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span id="enabledLeft"><p>Enabled: </p></span>' . "\n\t\t\t\t";

							//	If the user is the super admin
							if ($user->iD === 1){

									//	Don't allow the enabled checkbox to be changed
									$message .= '<span id="enabledRight"><input name="enabled" type="checkbox" checked="checked" value="true" disabled="disabled" /></span>' . "\n\t\t\t";

							}
							else {//	Not the super admin

								//	If the user is already enabled
								if ($user->enabled){

									//	Check the box
									$message .= '<span id="enabledRight"><input name="enabled" type="checkbox" checked="checked" value="true" /></span>' . "\n\t\t\t";

								}
								else {//	User is disabled

									//	Uncheck the box
									$message .= '<span id="enabledRight"><input name="enabled" type="checkbox" value="true" /></span>' . "\n\t\t\t";

								}
							}

							$message .= '</li>' . "\n\t\t\t";
							$message .= '<li>' . "\n\t\t\t\t";
							$message .= '<span><input id="saveUserBut" name="saveBut" type="button" value="save" onclick="save(' . $user->iD . ');" /></span>' . "\n\t\t\t\t";
							$message .= '<span><input id="cancelUserBut" name="cancelBut" type="button" value="cancel" onclick="cancel();" /></span>' . "\n\t\t\t";
							$message .= '</li>' . "\n\t\t";
							$message .= '</form>' . "\n\t";
							$message .= '</ul>';

						}

						break;

					case 'delete'://	User wants to delete an existing user

						//	If the user they want to delete is the admin
						if ($iD === 1){

							//	Deny them
							die('You cannot delete the administrative account!');

						}
						else if ($_SESSION['useriD'] == $iD){//	User wants to delete their own account

							//	Not sure how to deal with that just yet
							die('You cannot delete your own account');

						}
						else {//	User wants to delete another user

							// Include DbConnector object to query the database for images
							require_once('../spark/includes/DbConnector.php');
							$connector = new DbConnector();

							//	Prepare query to delete the user
							$query = 'DELETE FROM `' . $connector->dbTables['users'] . '` WHERE `iD`=' . $iD . ' LIMIT 1';
							
							//	If the query was successful
							if ($result = $connector->query($query)){

								//	Report the user has been deleted
								$message = '<div id="userMessage">' . "\n\t";
								$message .= '<p id="userMessageImg"><img src="/spark/images/successTick.png" alt="success" /></p>' . "\n\t";
								$message .= '<p id="userMessageTxt">User successfully deleted<br /></p>' . "\n\t";
								$message .= '</div>' . "\n";

							}
							else {

								//	Report the user could not be deleted
								$message = '<div id="userMessage">' . "\n\t";
								$message .= '<p id="userMessageImg"><img src="/spark/images/errorCross.png" alt="failure" /></p>' . "\n\t";
								$message .= '<p id="userMessageTxt">User was not deleted<br /></p>' . "\n\t";
								$message .= '</div>' . "\n";
							}
						}

						break;

					case 'save'://	User wants to save changes to an existing user

//	While I was trying to add the password edit fields
//print_r($_POST);
//die();

						// Include DbConnector object to query the database for users
						require_once('../spark/includes/DbConnector.php');
						$connector = new DbConnector();

						//	Start the query for updating the record
						$query = "UPDATE `" . $connector->dbTables['users'] . "` SET `firstName`='" . addslashes($_POST['firstName']) . "' , `lastName`='" . addslashes($_POST['lastName']) . "' ";

						//	If the phone number is not empty
						if ($_POST['phoneNumber'] != ''){

							//	Add it to the query
							$query .= ", `phone`='" . addslashes($_POST['phoneNumber']) . "' ";

						}

						//	If the user is not the super admin
						if ($iD !== 1){

							//	If the new group is admin
							if ($_POST['group'] == 'admin'){

								//	Append to the query appropriately
								$query .= ", `group`=0 ";

							}
							else {//	New group is user

								//	Append to the query appropriately
								$query .= ", `group`=1 ";

							}
						}
							
						//	If the user is enabled
						if ($_POST['enabled']){

							//	Append to the query appropriately
							$query .= " , `enabled`=1" ;

						}
						else {//	User is disabled

							//	As long as this isn't the super admin
							if ($iD !== 1){

								//	Append to the query appropriately
								$query .= " , `enabled`=0" ;

							}
						}

						//	Finish off the query
						$query .=  " WHERE `iD`=" . $iD;

						//	If the query ran successfully
						if ($result = $connector->query($query)){

							//	Report the user has been updated
							$message = '<div id="userMessage">' . "\n\t";
							$message .= '<p id="userMessageImg"><img src="/spark/images/successTick.png" alt="success" /></p>' . "\n\t";
							$message .= '<p id="userMessageTxt">Changes to user successfully saved<br /></p>' . "\n\t";
							$message .= '</div>' . "\n";

						}
						else {//	Something failed

							//	Report the user could not be updated
							$message = '<div id="userMessage">' . "\n\t";
							$message .= '<p id="userMessageImg"><img src="/spark/images/errorCross.png" alt="failure" /></p>' . "\n\t";
							$message .= '<p id="userMessageTxt">Changes to user could not be saved<br /></p>' . "\n\t";
							$message .= '</div>' . "\n";

						}
						break;

				}
			}
			else {//	There is no event or the event is invalid

				//	Redirect to the edit page for the useriD selected
				header('Location: /users/' . $_GET['iD'] . '/edit/');

			}
		}
		//	No iD, new user
		else if ((isset($_GET['event'])) && ($_GET['event'] == 'new')){

			//	Output the new user form
			$message .= "\n\t" . '<br />' . "\n\t";
			$message .= '<ul id="userDetails">' . "\n\t\t";
			$message .= '<form action="/users/" method="post">' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="userLeft"><p>Username: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="userRight"><input name="userName" type="text"></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="pass1Left"><p>Password: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="pass1Right"><input name="password1" type="password"></span>' . "\n\t\t\t\t";
			$message .= '<span class="userSpacer"><p>8 or more characters</p></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="pass2Left"><p>Repeat Password: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="pass2Right"><input name="password2" type="password"></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="nameFLeft"><p>First Name: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="nameFRight"><input name="firstName" type="text" /></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="nameLLeft"><p>Last Name: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="nameLRight"><input name="lastName" type="text" /></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="groupLeft"><p>Access Level: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="groupRight">' . "\n\t\t\t\t\t";
			$message .= '<select name="group">' . "\n\t\t\t\t\t\t";
			$message .= '<option value="admin">admin</option>' . "\n\t\t\t\t\t\t";
			$message .= '<option value="user" selected="selected">user</option>' . "\n\t\t\t\t\t";
			$message .= '</select>' . "\n\t\t\t\t";
			$message .= '</span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="phoneLeft"><p>Phone Number: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="phoneRight"><input name="phoneNumber" type="text" /></span>' . "\n\t\t\t\t";
			$message .= '<span class="userSpacer"><p>eg: +64 21 123 4567</p></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span id="enabledLeft"><p>Enabled: </p></span>' . "\n\t\t\t\t";
			$message .= '<span id="enabledRight"><input name="enabled" type="checkbox" checked="checked" value="true" /></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t\t";
			$message .= '<li>' . "\n\t\t\t\t";
			$message .= '<span><input id="saveUserBut" name="saveBut" type="button" value="save" onclick="save();" /></span>' . "\n\t\t\t\t";
			$message .= '<span><input id="cancelUserBut" name="cancelBut" type="button" value="cancel" onclick="cancel();" /></span>' . "\n\t\t\t";
			$message .= '</li>' . "\n\t\t";
			$message .= '</form>' . "\n\t";
			$message .= '</ul>';					

		}
		//	No iD, saving new user
		else if ((isset($_GET['event'])) && ($_GET['event'] == 'save')){

			// Include DbConnector object to query the database for images
			require_once('../spark/includes/DbConnector.php');
			$connector = new DbConnector();

			//	If phone number was specified
			if ($_POST['phoneNumber'] != ''){

				//	Prep the query to include it
				$query = "INSERT INTO `" . $connector->dbTables['users'] . "` (`username`,`password`,`firstName`,`lastName`,`group`,`phone`,`enabled`) VALUES ('" . addslashes($_POST['userName']) . "', PASSWORD('" . $_POST['password1'] . "'),'" . addslashes($_POST['firstName']) . "','" . addslashes($_POST['lastName']) . "',";

			}
			else {//	No phone number

				//	Prep the query to leave out phone number
				$query = "INSERT INTO `" . $connector->dbTables['users'] . "` (`username`,`password`,`firstName`,`lastName`,`group`,`enabled`) VALUES ('" . addslashes($_POST['userName']) . "', PASSWORD('" . $_POST['password1'] . "'),'" . addslashes($_POST['firstName']) . "','" . addslashes($_POST['lastName']) . "',";

			}			

			//	If the new group is admin
			if ($_POST['group'] == 'admin'){

				//Append to the query appropriately
				$query .= "'0',";

			}
			else {//	New group is user

				//Append to the query appropriately
				$query .= "'1',";

			}

			//	If phone number was specified
			if ($_POST['phoneNumber'] != ''){

				//Append to the query appropriately
				$query .= "'" . addslashes($_POST['phoneNumber']) . "',";

			}

			//	If the user is to be enabled
			if (isset($_POST['enabled'])){

				//Append to the query appropriately
				$query .= "'1')";

			}
			else {//	User will be disabled
			
				//Append to the query appropriately
				$query .= "'0')";
				
			}

			//	If the query ran successfully
			if ($result = $connector->query($query)){

				//	Report the user has been added
				$message = '<div id="userMessage">' . "\n\t";
				$message .= '<p id="userMessageImg"><img src="/spark/images/successTick.png" alt="success" /></p>' . "\n\t";
				$message .= '<p id="userMessageTxt">New user successfully added<br /></p>' . "\n\t";
				$message .= '</div>' . "\n";

			}
			else {//	The query failed

				//	Report the user could not be added
				$message = '<div id="userMessage">' . "\n\t";
				$message .= '<p id="userMessageImg"><img src="/spark/images/errorCross.png" alt="failure" /></p>' . "\n\t";
				$message .= '<p id="userMessageTxt">New user could not be added' . "\n\t";
				$message .= '</div>' . "\n";

			}
		}

		// Include DbConnector object to query the database for images
		require_once('../spark/includes/DbConnector.php');
		$connector = new DbConnector();

		//	Prep query to grab all users from the db
		$query = "SELECT `" . $connector->dbTables['users'] . "`.`iD`, `" . $connector->dbTables['users'] . "`.`username`, `" . $connector->dbTables['users'] . "`.`firstName`, `" . $connector->dbTables['users'] . "`.`lastName`, `" . $connector->dbTables['users'] . "`.`phone`, `" . $connector->dbTables['users'] . "`.`enabled`, `" . $connector->dbTables['userGroups'] . "`.`name` AS `group` FROM `" . $connector->dbTables['users'] . "`, `" . $connector->dbTables['userGroups'] . "` WHERE (`" . $connector->dbTables['users'] . "`.`group` = `" . $connector->dbTables['userGroups'] . "`.`iD`) ORDER BY `" . $connector->dbTables['users'] . "`.`iD` ASC";

		//	Perform query
		if ($result = $connector->query($query)){

			//	Store number of results for later use
			$count = $connector->getNumRows($result);

			//	As long as there's at least 1 result
			if ($count > 0){

				//	Begin output of users table
				if (!isset($message)){

					$message = "\n\t" . '<br />' . "\n\t";

				}
				else {

					$message .= "\n\t" . '<br />' . "\n\t";

				}

				$message .= '<div id="tableWrapper">' . "\n\t\t";
				$message .= '<div id="headerRow">' . "\n\t\t\t";
				$message .= '<span id="idHeader">iD</span>' . "\n\t\t\t";
				$message .= '<span id="userHeader">username</span>' . "\n\t\t\t";
				$message .= '<span id="nameHeader">name</span>' . "\n\t\t\t";
				$message .= '<span id="groupHeader">group</span>' . "\n\t\t\t";
				$message .= '<span id="phoneHeader">phone</span>' . "\n\t\t\t";
				$message .= '<span id="enabledHeader">enabled</span>' . "\n\t\t";
				$message .= '</div>' . "\n\t\t";

				//	Loop for each user returned
				for ($i = 0; $i < $count; $i++){

					//	Prepare the user object for later use
					$user = $connector->fetchObject($result);

					//	Alternate row colours starting with userRowA
					if (!($i % 2)){

						$row = '<div class="userRowA">' . "\n\t\t\t";

					}
					else {

						$row = '<div class="userRowB">' . "\n\t\t\t";

					}

					//	Continue output of the user
					$row .= '<span class="id">' . $user->iD . '</span>' . "\n\t\t\t";
					$row .= '<span class="user">' . $user->username . '</span>' . "\n\t\t\t";
					$row .= '<span class="name">' . stripslashes($user->firstName) . ' ' . stripslashes($user->lastName) . '</span>' . "\n\t\t\t";
					$row .= '<span class="group">' . $user->group . '</span>' . "\n\t\t\t";

					//	If the user has a phone number
					if ($user->phone != null){

						//	Output the number
						$row .= '<span class="phone">' . stripslashes($user->phone) . '</span>' . "\n\t\t\t";

					}
					else {//	User has no phone number

						//	Output a spacer
						$row .= '<span class="phone">&nbsp;</span>' . "\n\t\t\t";

					}

					//	If the user is enabled
					if ($user->enabled){

						//	Output a green 'Yes'
						$row .= '<span class="enabled"><p>Yes</p></span>' . "\n\t\t\t";

					}
					else {//	User is disabled

						//	Output a red 'No'
						$row .= '<span class="disabled"><p>No</p></span>' . "\n\t\t\t";

					}

					//	Output an edit link
					$row .= '<span class="edit"><a href="/users/' . $user->iD . '/edit/" title="edit">edit</a></span>' . "\n\t\t\t";

					//	If the user is the admin or the currently logged in user
					if (($user->iD === 1) || ($user->iD == $_SESSION['useriD'])){

						//	Don't output the delete link, but keep spacing the output
						$row .= "\n\t\t";

					}
					else {//	User isn't the admin or the currently logged in user

						//	Output a delete link
						$row .= '<span class="delete"><a href="javascript:delUser(' . $user->iD . ');" title="delete">delete</a></span>' . "\n\t\t";

					}

					//	If this is the last iteration of the loop
					if (($i + 1) == $count){

						//	Output different spacing than normal
						$row .= '</div>' . "\n\t";

					}
					else {//	Not the last iteration of the loop

						//	Output the normal spacing
						$row .= '</div>' . "\n\t\t";

					}

					//	Output the user row
					$message .= $row;

				}

				//	Tidy up the end of the user table
				$message .= '</div>' . "\n\t";

				//	Output an image to space out the body
				$message .= '<img src="/spark/images/spacer.png" width="1px" height="1px" />' . "\n\t";

				//	Output the add user button
				$message .= '<p id="addUserBut"><input name="addUser" type="button" value="add user" onclick="addUser();" /></p>' . "\n\t";

				//	Output an image to space out the body
				$message .= '<img src="/spark/images/spacer.png" width="1px" height="' . (55+($count * 25)) . 'px" />' . "\n";

			}
		}
	}

	//	Include Header object to ouput basic header information
	require_once('../spark/includes/Header.php');
	$header = new Header();

?>
<style type="text/css">
	<!--
		@import url("/spark/styles/header.css");
		@import url("/spark/styles/users.css");		
	-->
</style>
<script type="text/javascript" src="/spark/scripts/users.js"></script>

<title>spark.users</title>
</head>
<body>
<?php

	//	Generate navigation tabs required for this page
	$header->tabs('users');

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