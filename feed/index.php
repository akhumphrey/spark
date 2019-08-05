<?php

/*

	Name: /feed/index.php
	Purpose: Provides rss output of the 10 most recently uploaded images
	Revision: 1

*/

	//	Set the content type to xml so clients aren't confused
	header('Content-type: text/xml'); 

	//	Begin output of the rss document
	echo '<?xml version="1.0"' . '?>';
	echo "\n" . '<?xml-stylesheet title="CSS_formatting" type="text/css" href="/spark/styles/rss.css"' . '?>';
/*	echo "\n" . '<?xml-stylesheet title="XSL_formatting" type="text/xsl" href="/spark/styles/rss.xsl"' . '?>';*/
	echo "\n" . '<rss version="2.0">';
	echo "\n\t" . '<channel>';
	echo "\n\t\t" . '<title>spark.images</title>';
	echo "\n\t\t" . '<description>The latest image uploads from spark.images</description>';
	echo "\n\t\t" . '<link>http://' . $_SERVER['SERVER_NAME'] . '/images/</link>';
	echo "\n\t\t" . '<copyright>Copyright 2005 emberDesign, all rights reserved.</copyright>';

	// Include DbConnector object to query the database for users
	require_once('../spark/includes/DbConnector.php');
	$connector = new DbConnector();

	//	Include File object for file information
	require_once('../spark/includes/File.php');
	$file = new File();

	//	Define the query which will grab the latest uploads
	$query = 'SELECT `iD`,`name`,`owner`,`pathM`,`format`,`category`,`size`,`width`,`height`,`keywords`,`timeStamp` FROM `' . $connector->dbTables['images'] . '` WHERE `enabled` = 1 ORDER BY `timeStamp` DESC LIMIT 0,10';

	//	If the query runs successfully
	if ($result = $connector->query($query)){

		//	Loop through the results
		while ($item = $connector->fetchObject($result)){

			//	Output each result in rss/xml format, cleaning the text
			echo "\n\t\t" . '<item>';
			echo "\n\t\t\t" . '<title>' . htmlentities(strip_tags($item->name)) . '</title>';
			echo "\n\t\t\t" . '<description>';
			echo "\n\t\t\t\t" . '&lt;img src=&quot;http://' . $_SERVER['SERVER_NAME'] . $item->pathM . '&quot; alt=&quot;' . htmlentities(strip_tags($item->name)) . '&quot; /&gt;&lt;br /&gt;&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Name: ' . htmlentities(strip_tags($item->name)) . '&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Owner: ' . htmlentities(strip_tags($item->owner)) . '&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Format: ' . htmlentities(strip_tags($item->format)) . '&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Dimensions: ' . htmlentities(strip_tags($item->width)) . 'x' . htmlentities(strip_tags($item->height)) . '&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Filesize: ' . $file->getFileSize(htmlentities($item->size)) . '&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Category: ' . htmlentities(strip_tags($item->category)) . '&lt;br /&gt;';
			echo "\n\t\t\t\t" . 'Keywords: ' . htmlentities(strip_tags($item->keywords)) . '&lt;br /&gt;';
			echo "\n\t\t\t" . '</description>';
			echo "\n\t\t\t" . '<link>http://' . $_SERVER['SERVER_NAME'] . '/images/details/' . $item->iD . '/</link>';
			echo "\n\t\t\t" . '<pubDate>' . date('D, d M Y g:i:s O', (int)$item->timeStamp) . '</pubDate>';
			echo "\n\t\t" . '</item>';

		}
	}

	//	Close off the document
	echo "\n\t" . '</channel>' . "\n" . '</rss>';

?>