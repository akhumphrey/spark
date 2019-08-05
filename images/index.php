<?php

/*

	Name: /images/index.php
	Purpose: Provides search / browse facilities for the gallery
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

	//	Unset the page target since they should have successfully logged in now
	unset($_SESSION['target']);

	//	Include Validator object for input testing
	require_once('../spark/includes/Validator.php');
	$validator = new Validator();

	// Include DbConnector object to query the database for images
	require_once('../spark/includes/DbConnector.php');
	$connector = new DbConnector();

	//	User is searching
	if ((isset($_POST['event'])) && ($_POST['event'] == 'search')){

		//	Prepare the start of the query
		$query = "SELECT * FROM `" . $connector->dbTables['images'] . "` WHERE ";

		//	If there are valid keywords
		if ((isset($_POST['filterKeywords'])) && ($validator->looseText($_POST['filterKeywords']))){
		
			//	Retain the keywords for later use
			$_SESSION['keys'] = $_POST['filterKeywords'];

			//	Destroy the $_POST variable
			unset($_POST['filterKeywords']);

			//	If a comma occurs in the keywords field
			if (strrpos($_SESSION['keys'], ',')){

				//	Create a comma-seperated array of search terms
				$keyArray = explode(',', trim($_SESSION['keys']));

			}
			//	If a semicolon occurs in the keywords field
			else if (strrpos($_SESSION['keys'], ';')){

				//	Create a semicolon-seperated array of search terms
				$keyArray = explode(';', trim($_SESSION['keys']));

			}
			else {

				//	Create a space-seperated array of search terms
				$keyArray = explode(' ', trim($_SESSION['keys']));

			}

			//	Append the start of a keyword search to the query
			$query .= "`keywords` LIKE '%";

			//	If there is more than 1 keyword
			if (count($keyArray) > 1){

				//	Loop through each keyword
				for ($i = 0; $i < count($keyArray); $i++){

					//	If this is the last keyword
					if ($i == (count($keyArray) - 1)){

						//	Append the last of the keywords to the query and prep to move on
						$query .= $keyArray[$i] . "%' AND ";

					}
					else {//	This isn't the last keyword

						//	Append the current keyword and prep for more
						$query .= $keyArray[$i] . "%' AND `keywords` LIKE '%";

					}
				}
			}
			else {//	There is only 1 keyword

				//	Append the keyword to the query and prep to move on
				$query .= $keyArray[0] . "%' AND ";

			}
		}
		else {//	There are no keywords

			//	Reset the keywords to blank (NOT null)
			$_SESSION['keys'] = '';

		}

		//	If there is a valid category
		if ((isset($_POST['filterCategory'])) && ($validator->strictText($_POST['filterCategory']))){

			//	Retain the category
			$_SESSION['category'] = $_POST['filterCategory'];

			//	Destroy the $_POST variable
			unset($_POST['filterCategory']);
			
			//	If the category isn't the 'all' placeholder
			if ($_SESSION['category'] != 'all'){

				//	Use the category & move on
				$query .= "`category`='" . $_SESSION['category'] . "' AND `enabled`='1' ";

			}
			else {//	Category is the 'all' placeholder
			
				//	Move on
				$query .= "`enabled`='1' ";
				
			}
			
		}

		//	If there is a valid column to order by
		if ((isset($_POST['filterSortBy'])) && ($validator->strictText($_POST['filterSortBy']))){

			//	Retain the column name
			$_SESSION['sortBy'] = $_POST['filterSortBy'];

			//	Destroy the $_POST variable
			unset($_POST['filterSortBy']);

			//	Append the ordering prefix
			$query .= "ORDER BY ";

			//	Determin the correct column name and append the column name to the query
			if ($_SESSION['sortBy'] == 'owner'){

				$query .= "`owner` ";

			}
			else if ($_SESSION['sortBy'] == 'iD'){

				$query .= "`iD` ";

			}
			else if ($_SESSION['sortBy'] == 'date'){

				$query .= "`timeStamp` ";

			}
			else {

				$query .= "`name` ";

			}
		}

		//	If there is a valid sort direction
		if ((isset($_POST['filterDirection'])) && ($validator->strictText($_POST['filterDirection']))){

			//	Retain the direction
			$_SESSION['direction'] = $_POST['filterDirection'];

			//	Destroy the $_POST variable
			unset($_POST['filterDirection']);

			//	Determin the correct direction and append the direction to the query
			if ($_SESSION['direction'] == 'asc'){

				$query .= "ASC ";

			}
			else {

				$query .= "DESC ";

			}

		}

		//	If there is a valid display mode
		if ((isset($_POST['filterDisplay'])) && ($validator->strictText($_POST['filterDisplay']))){

			//	Retain the display mode for later use
			$_SESSION['displayMode'] = $_POST['filterDisplay'];

			//	Destroy the $_POST variable
			unset($_POST['filterDisplay']);

		}

		//	encode the query and place it into $_SESSION for future use
		$_SESSION['search'] = base64_encode($query);

		//	Clear the post event
		unset($_POST['event']);

	}

	//	If there is a new page limit
	if (isset($_POST['filterLimit'])){

		// Use the new limit
		$pageLimit = $_POST['filterLimit'];

		//	Store the limit
		$_SESSION['pageLimit'] = $pageLimit;

		//	Clear the sent limit
		unset($_POST['filterLimit']);

	}
	//	If there is already a limit
	else if (isset($_SESSION['pageLimit'])){

		//	Grab the limit for use further down
		$pageLimit = $_SESSION['pageLimit'];

	}
	else {

		//	There isn't yet a limit, set a reasonable one
		$pageLimit = 12;

		//	Store the limit
		$_SESSION['pageLimit'] = $pageLimit; 

	}

	//	If the user is browsing to another page in a search
	if (isset($_GET['page'])){

		//	Store the requested page
		$_SESSION['currentPage'] = $_GET['page'];

	}
	else {//	Not browsing to the next page

		//	Default to page 1
		$_SESSION['currentPage'] = 1;

	}

	//	Include Pager object to generate page offset information
	require_once('../spark/includes/Pager.php');
	$pager = new Pager();

	//	If there isn't already a query
	if (!isset($_SESSION['search'])){

		//	Generate a default query for later use
		$query = "SELECT * FROM `" . $connector->dbTables['images'] . "` WHERE `enabled`=1 ORDER BY `name` ASC";

	}
	else {//	Already a query
	
		//	Decode the query
		$query = base64_decode($_SESSION['search']);
		
	}

	//	If query was successful
	if ($result = $connector->query($query)){

		//	Count number of returned results
		$count = $connector->getNumRows($result);

		//	Calculate paging information
		$numPages = $pager->getPagerData($count,$pageLimit,$_SESSION['currentPage']);

		//	Append page limits to the query
		$query .= ' LIMIT ' . $numPages->offset . ',' . $numPages->limit;

		//	If this is the last or only page of results
		//	Keep track of the number of items on the page (for use with list display mode)
		if ((($count - (($numPages->offset + 1) + $numPages->limit)) <= 0) || ($numPages->limit > $count)){

			$countPageItems = $count - $numPages->offset;

		}
		else {

			$countPageItems = $numPages->limit;

		}

		//	Resend the query with added limits
		$result = $connector->query($query);

	}

	//	Include Header object to ouput basic header information
	require_once('../spark/includes/Header.php');
	$header = new Header();


?>
<style type="text/css">
	<!--
		@import url("/spark/styles/header.css");
		@import url("/spark/styles/images.css");
		@import url("/spark/styles/imagesList.css");
	-->
</style>
<script type="text/javascript" src="/spark/scripts/images.js"></script>

<title>spark.images</title>
</head>
<body>
<?php

	//	Generate navigation tabs required for this page
	$header->tabs('images');

?>
<div id="bodyWrapper">
	<form action="/images/" method="post" id="filterForm">
		<div id="filterKeys">
			<p>keywords:</p>
			<?php

				//	If there are keywords from a search
				if (isset($_SESSION['keys'])){

					//	Output filled input box
					echo '<input name="filterKeywords" type="text" size="12" value="' . $_SESSION['keys'] . '" />' . "\n";

				}
				else {//	No previous keywords

					//	Output plain input box
					echo '<input name="filterKeywords" type="text" size="12" />' . "\n";

				}

			?>
		</div>
		<div id="filterCat">
			<p>category:</p>
			<select name="filterCategory">
				<?php

					//	If there is a category set output the appropriately selected option
					switch ($_SESSION['category']) {
						
						case 'misc':

							echo '<option value="all">all</option>' . "\n\t\t\t\t" . '<option value="misc" selected="selected">misc</option>' . "\n\t\t\t\t" . '<option value="wallpapers">wallpapers</option>' . "\n";
							break;

						case 'wallpapers':

							echo '<option value="all">all</option>' . "\n\t\t\t\t" . '<option value="misc">misc</option>' . "\n\t\t\t\t" . '<option value="wallpapers" selected="selected">wallpapers</option>' . "\n";
							break;

						default:

							echo '<option value="all" selected="selected">all</option>' . "\n\t\t\t\t" . '<option value="misc">misc</option>' . "\n\t\t\t\t" . '<option value="wallpapers">wallpapers</option>' . "\n";
							break;

					}

				?>
			</select>
		</div>
		<div id="filterSort">
			<p>sort by:</p>
			<select name="filterSortBy">
				<?php

					//	If there is a sort column set output the appropriately selected option
					switch ($_SESSION['sortBy']) {

						case 'iD':

							echo '<option value="name">name</option>' . "\n\t\t\t\t" . '<option value="date">date</option>' . "\n\t\t\t\t" . '<option value="owner">owner</option>' . "\n\t\t\t\t" . '<option value="iD" selected="selected">iD</option>' . "\n";
							break;

						case 'owner':

							echo '<option value="name">name</option>' . "\n\t\t\t\t" . '<option value="date">date</option>' . "\n\t\t\t\t" . '<option value="owner" selected="selected">owner</option>' . "\n\t\t\t\t" . '<option value="iD">iD</option>' . "\n";
							break;

						case 'date':

							echo '<option value="name">name</option>' . "\n\t\t\t\t" . '<option value="date" selected="selected">date</option>' . "\n\t\t\t\t" . '<option value="owner">owner</option>' . "\n\t\t\t\t" . '<option value="iD">iD</option>' . "\n";
							break;

						default:

							echo '<option value="name" selected="selected">name</option>' . "\n\t\t\t\t" . '<option value="date">date</option>' . "\n\t\t\t\t" . '<option value="owner">owner</option>' . "\n\t\t\t\t" . '<option value="iD">iD</option>' . "\n";
							break;

					}

				?>
			</select>
		</div>
		<div id="filterDir">
			<p>order:</p>
			<select name="filterDirection">
				<?php

					//	If the sorting direction is descending
					if ((isset($_SESSION['direction'])) && ($_SESSION['direction'] == 'desc')){

						//	Output the appropriately selected option
						echo '<option value="asc">asc</option>' . "\n\t\t\t\t" . '<option value="desc" selected="selected">desc</option>' . "\n";

					}
					else {//	Ascending direction or none set

						//	Output the default set of options
						echo '<option value="asc" selected="selected">asc</option>' . "\n\t\t\t\t" . '<option value="desc">desc</option>' . "\n";

					}

				?>
			</select>
		</div>
		<div id="filterDisp">
			<p>display mode:</p>
			<select name="filterDisplay">
				<?php

					//	If the display mode is list
					if ((isset($_SESSION['displayMode'])) && ($_SESSION['displayMode'] == 'list')){

						//	Output the appropriately selected option
						echo '<option value="thumbnails">thumbnails</option>' . "\n\t\t\t\t" . '<option value="list" selected="selected">list</option>' . "\n";

					}
					else {//	Thumbnails or none set

						//	Output the default set of options
						echo '<option value="thumbnails" selected="selected">thumbnails</option>' . "\n\t\t\t\t" . '<option value="list">list</option>' . "\n";

					}

				?>
			</select>
		</div>
		<div id="filterLim">
			<p>limit:</p>
			<select name="filterLimit">
				<?php

					//	If there is a page limit set output the appropriately selected option
					switch ($_SESSION['pageLimit']) {
						
						case '36':
						
							echo '<option value="12">12</option>' . "\n\t\t\t\t" . '<option value="36" selected="selected">36</option>' . "\n\t\t\t\t" . '<option value="60">60</option>' . "\n\t\t\t\t" . '<option value="120">120</option>' . "\n";
							break;
								
						case '60':

							echo '<option value="12">12</option>' . "\n\t\t\t\t" . '<option value="36">36</option>' . "\n\t\t\t\t" . '<option value="60" selected="selected">60</option>' . "\n\t\t\t\t" . '<option value="120">120</option>' . "\n";
							break;
							
						case '120':

							echo '<option value="12">12</option>' . "\n\t\t\t\t" . '<option value="36">36</option>' . "\n\t\t\t\t" . '<option value="60">60</option>' . "\n\t\t\t\t" . '<option value="120" selected="selected">120</option>' . "\n";
							break;

						default:

							echo '<option value="12" selected="selected">12</option>' . "\n\t\t\t\t" . '<option value="36">36</option>' . "\n\t\t\t\t" . '<option value="60">60</option>' . "\n\t\t\t\t" . '<option value="120">120</option>' . "\n";
							break;

					}

				?>
			</select>
		</div>
		<div><input name="event" type="hidden" /></div>
		<div id="filterApply">
			<br /><input name="filterApplyBtn" type="button" value="apply" onclick="validate();" />
		</div>
		<div id="filterAdd">
			<br /><input name="filterAddBtn" type="button" value="add image(s)" onclick="upload();" />
		</div>
	</form>
	<div id="galleryWrapper">
		<div id="galleryResults">
			<div id="galleryNum"><?php

				//	If there were no results
				if ($count <= 0){

					//	Output no results message
					echo 'Sorry, no images matched your search.</div>' . "\n\t\t";

				}
				else { //	There was at least 1 result

					//	If there is only 1 match
					if ($count == 1){

						//	Report the singluar result
						echo '1 match.';

					}
					else {//	More than 1 result

						//	Tidy up the number of results count for the last (or only) page of results
						if ((($count - (($numPages->offset + 1) + $numPages->limit)) <= 0) || ($numPages->limit > $count)){

							//	Last (or only) page of results
							echo ($numPages->offset + 1) . '-' . $count . ' of ' . $count . ' matches.';

						}
						else {

							//	Not the last page of results
							echo ($numPages->offset + 1) . '-' . ($numPages->offset + $numPages->limit) . ' of ' . $count . ' matches.';

						}
					}
				}
				?></div>
		<?php

			//	If there was a result, output the gallery
			if ($count > 0){

				//	Start outputting the pager navigation
				echo "\t\t\t" . '<ul id="galleryPaging">';
				echo "\n\t\t\t\t" . '<li>pages:</li>';

				//	If this isn't the first page of several
				if ($numPages->page > 1){

					//	Output a less-than linking to the previous page
					echo "\n\t\t\t\t" . '<li title="previous page"><a href="/images/search/' . ($numPages->page - 1) . '/">&lt;</a></li>';

				}
				else {//	This is the first page of several

					//	Output an unlinked less-than
					echo "\n\t\t\t\t" . '<li title="previous page">&lt;</li>';

				}

				//	For each page that's needed
				for ($i=1; $i < ($numPages->numPages + 1); $i++){

					//	If this is the first page
					if ($i == $numPages->page){

						//	Output an unlinked 1
						echo "\n\t\t\t\t" . '<li title="page ' . $i . ' of ' . $numPages->numPages . '">' . $i . '</li>';

					}
					else {//	This isn't the first page

						//	Output a linked page number
						echo "\n\t\t\t\t" . '<li title="page ' . $i . ' of ' . $numPages->numPages . '"><a href="/images/search/' . $i . '/">' . $i . '</a></li>';

					}
				}

				//	If this is the last page of several
				if ($numPages->page == $numPages->numPages){

					//	Output an unlinked greater-than
					echo "\n\t\t\t\t" . '<li title="next page">&gt;</li>';

				}
				else {//	This isn't the last page of several

					//	Output a linked greater-than
					echo "\n\t\t\t\t" . '<li title="next page"><a href="/images/search/' . ($numPages->page + 1) . '/">&gt;</a></li>';

				}

				//	Close off the pager navigation
				echo "\n\t\t\t" . '</ul>';
				echo "\n\t\t" . '</div>';
				echo "\n\t\t" . '<p class="gallerySpacer">&nbsp;</p>' . "\n";

				//	If the display mode is list
				if ($_SESSION['displayMode'] == 'list'){

					//	Include File object for file information
					require_once('../spark/includes/File.php');
					$file = new File();

					//	Start outputting the table
					echo "\n\t\t" . '<div id="tableWrapper">';
					echo "\n\t\t\t" . '<ul id="headerRow">';
					echo "\n\t\t\t\t" . '<li id="nameHeader">name</span>';
					echo "\n\t\t\t\t" . '<li id="ownerHeader">owner</span>';
					echo "\n\t\t\t\t" . '<li id="dateHeader">date</span>';
					echo "\n\t\t\t\t" . '<li id="formatHeader">format</span>';
					echo "\n\t\t\t\t" . '<li id="dimensionsHeader">dimensions</span>';
					echo "\n\t\t\t\t" . '<li id="sizeHeader">filesize</span>';
					echo "\n\t\t\t\t" . '<li id="categoryHeader">category</span>';
					echo "\n\t\t\t" . '</ul>';

					//	Loop for each image on the page
					for ($j = 0; $j < $countPageItems; $j++){

						//	Prepare the user object for later use
						$image = $connector->fetchObject($result);

						//	Alternate row colours starting with imageRowA
						if (!($j % 2)){

							echo "\n\t\t\t" . '<ul class="imageRowA">';

						}
						else {

							echo "\n\t\t\t" . '<ul class="imageRowB">';

						}

						//	Continue output of the image
						echo "\n\t\t\t\t" . '<li class="name"><a href="/images/details/' . $image->iD . '/" title="' . stripslashes($image->name) . '">' . stripslashes($image->name) . '</a></li>';
						echo "\n\t\t\t\t" . '<li class="owner">' . stripslashes($image->owner) . '</li>';
						echo "\n\t\t\t\t" . '<li class="date">' . date('d/m/Y', (int)$image->timeStamp) . '</li>';
						echo "\n\t\t\t\t" . '<li class="format">' . $image->format . '</li>';
						echo "\n\t\t\t\t" . '<li class="dimensions">' . $image->width . 'x' . $image->height . 'px</li>';
						echo "\n\t\t\t\t" . '<li class="size">' . $file->getFileSize($image->size) . '</li>';
						echo "\n\t\t\t\t" . '<li class="category">' . stripslashes($image->category) . '</li>';
						echo "\n\t\t\t" . '</ul>';

					}

					//	Finish off the table and space it out
					echo "\n\t\t" . '</div>';
					echo "\n\t\t" . '<p id="listSpacer">&nbsp;</p>' . "\n";
					
				}
				else {//	Display mode thumbnails

					//	Loop until there are no more results
					while ($image = $connector->fetchObject($result)){

						//	Output the image information for the current iteration
						echo "\n\t\t" . '<div class="imageWrapper"><a href="/images/details/' . $image->iD . '/"><img src="' . $image->pathS . '" alt="' . $image->name . '" class="image" /></a>' . "\n\t\t";
						echo "\t" . '<ul class="imageDetails">' . "\n\t\t\t\t" . '<a href="/images/details/' . $image->iD . '/"><li class="imageName" title="name: ' . $image->name . '">' . $image->name . '</li></a>';
						echo "\n\t\t\t\t" . '<li class="imageOwner" title="owner: ' . $image->owner . '">' . $image->owner . '</li>';
						echo "\n\t\t\t\t" . '<li class="imageRes" title="resolution: ' . $image->width . 'x' . $image->height . '">' . $image->width . 'x' . $image->height . '</li>';
						echo "\n\t\t\t" . '</ul>';
						echo "\n\t\t" . '</div>' . "\n";

					}
				}
			}
			else {

					//	Pad out the 'no results' page a bit, so it doesn't look so cramped
					echo "\n\t\t" . '<div class="imageWrapper"><img src="/spark/images/spacer.png" alt="" class="gallerySpacer" /><br /></div>' . "\n\t\t";

			}

		?>
		<p class="gallerySpacer">&nbsp;</p>
	</div>
</div>
<?php

	//	Generate footer and copyright information
	require_once('../spark/includes/Footer.php');
	$footer = new Footer();

?>
</body>
</html>