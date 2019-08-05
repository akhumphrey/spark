<?php

/*

	Class: Header
	Purpose: Outputs page header info / tabs
	Revision: 1

*/


class Header {

	//	Function: __construct, Purpose: output the header of the page
	public function __construct(){

		echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"' . "\n\t" . '"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">' . "\n";
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">' . "\n";
		echo '<head>' . "\n";
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
		echo '<meta name="rating" content="General" />' . "\n";
		echo '<meta name="language" content="en" />' . "\n";
		echo '<meta name="copyright" content="&copy; 2005" />' . "\n";
		echo '<meta name="author" content="Adrian Humphrey - ember.co.nz" />' . "\n";
		echo '<meta name="generator" content="Bluefish 1.01" />' . "\n";
		echo '<meta name="robots" content="all" />' . "\n";
		echo '<meta name="GOOGLEBOT" content="NOARCHIVE" />' . "\n";
		echo '<meta name="Expires" content="0" />' . "\n";
		echo '<meta name="Keywords" content="" />' . "\n";
		echo '<meta name="Description" content="" />' . "\n";
		echo '<link rel="alternate" type="application/rss+xml" title="spark.images" href="/feed/" />' . "\n\n";
		echo '<style type="text/css">' . "\n\t";
		echo '<!--' . "\n\t\t";
		echo '@import url("/spark/styles/common.css");' . "\n\t";
		echo '-->' . "\n";
		echo '</style>' . "\n";
		echo '<!--[if IE]>' . "\n\t";
		echo '<style type="text/css">' . "\n\t\t";
		echo '@import url("/spark/styles/ie.css");' . "\n\t";
		echo '</style>' . "\n";
		echo '<![endif]-->' . "\n";

	}

	//	Function tabs, Purpose: output the relevant tabs for the required page
	public function tabs($activeTab = '') {

		echo '<div id="header">' . "\n";

		switch ($activeTab) {

			case 'home':

				echo "\t" . '<div id="headerImg"><img src="/spark/images/headerSpark.png" alt="spark.home" /><img src="/spark/images/headerDot.png" alt="spark.home" /><img src="/spark/images/headerHome.png" alt="spark.home" /></div>' . "\n";
				echo "\t" . '<ul id="headerTabs">' . "\n";
				echo "\t\t" . '<li><p id="activeTab" title="home">home</p></li>' . "\n";
				echo "\t\t" . '<li><a href="/contracts/" title="contracts">contracts</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/images/" title="images">images</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/invoices/" title="invoices">invoices</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/clients/" title="clients">clients</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/users/" title="users">users</a></li>' . "\n";
				break;

			case 'contracts':

				echo "\t" . '<div id="headerImg"><img src="/spark/images/headerSpark.png" alt="spark.contracts" /><img src="/spark/images/headerDot.png" alt="spark.contracts" /><img src="/spark/images/headerContracts.png" alt="spark.contracts" /></div>' . "\n";
				echo "\t" . '<ul id="headerTabs">' . "\n";
				echo "\t\t" . '<li><a href="/home/" title="home">home</a></li>' . "\n";
				echo "\t\t" . '<li><p id="activeTab" title="contracts">contracts</p></li>' . "\n";
				echo "\t\t" . '<li><a href="/images/" title="images">images</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/invoices/" title="invoices">invoices</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/clients/" title="clients">clients</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/users/" title="users">users</a></li>' . "\n";
				break;

			case 'images':

				echo "\t" . '<div id="headerImg"><img src="/spark/images/headerSpark.png" alt="spark.images" /><img src="/spark/images/headerDot.png" alt="spark.images" /><img src="/spark/images/headerImages.png" alt="spark.images" /></div>' . "\n";
				echo "\t" . '<ul id="headerTabs">' . "\n";
				echo "\t\t" . '<li id="homeTab"><a href="/home/" title="home">home</a></li>' . "\n";
				echo "\t\t" . '<li id="contTab"><a href="/contracts/" title="contracts">contracts</a></li>' . "\n";
				echo "\t\t" . '<li id="imgTab"><p id="activeTab" title="images">images</p></li>' . "\n";
				echo "\t\t" . '<li id="invTab"><a href="/invoices/" title="invoices">invoices</a></li>' . "\n";
				echo "\t\t" . '<li id="cliTab"><a href="/clients/" title="clients">clients</a></li>' . "\n";
				echo "\t\t" . '<li id="userTab"><a href="/users/" title="users">users</a></li>' . "\n";
				break;

			case 'invoices':

				echo "\t" . '<div id="headerImg"><img src="/spark/images/headerSpark.png" alt="spark.invoices" /><img src="/spark/images/headerDot.png" alt="spark.invoices" /><img src="/spark/images/headerInvoices.png" alt="spark.invoices" /></div>' . "\n";
				echo "\t" . '<ul id="headerTabs">' . "\n";
				echo "\t\t" . '<li><a href="/home/" title="home">home</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/contracts/" title="contracts">contracts</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/images/" title="images">images</a></li>' . "\n";
				echo "\t\t" . '<li><p id="activeTab" title="invoices">invoices</p></li>' . "\n";
				echo "\t\t" . '<li><a href="/clients/" title="clients">clients</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/users/" title="users">users</a></li>' . "\n";
				break;

			case 'clients':

				echo "\t" . '<div id="headerImg"><img src="/spark/images/headerSpark.png" alt="spark.clients" /><img src="/spark/images/headerDot.png" alt="spark.clients" /><img src="/spark/images/headerClients.png" alt="spark.clients" /></div>' . "\n";
				echo "\t" . '<ul id="headerTabs">' . "\n";
				echo "\t\t" . '<li><a href="/home/" title="home">home</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/contracts/" title="contracts">contracts</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/images/" title="images">images</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/invoices/" title="invoices">invoices</a></li>' . "\n";
				echo "\t\t" . '<li><p id="activeTab" title="clients">clients</p></li>' . "\n";
				echo "\t\t" . '<li><a href="/users/" title="users">users</a></li>' . "\n";
				break;

			case 'users':

				echo "\t" . '<div id="headerImg"><img src="/spark/images/headerSpark.png" alt="spark.users" /><img src="/spark/images/headerDot.png" alt="spark.users" /><img src="/spark/images/headerUsers.png" alt="spark.users" /></div>' . "\n";
				echo "\t" . '<ul id="headerTabs">' . "\n";
				echo "\t\t" . '<li><a href="/home/" title="home">home</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/contracts/" title="contracts">contracts</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/images/" title="images">images</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/invoices/" title="invoices">invoices</a></li>' . "\n";
				echo "\t\t" . '<li><a href="/clients/" title="clients">clients</a></li>' . "\n";
				echo "\t\t" . '<li><p id="activeTab" title="users">users</p></li>' . "\n";
				break;

			default:

				echo '<p><strong>You didn\'t enter an activeTab</strong></p>';
				break;

		}

		echo "\t\t" . '<li id="outTab"><a href="/logout/" id="logout" title="logout">logout</a></li>' . "\n";
		echo "\t" . '</ul>' . "\n";
		echo '</div>' . "\n";

	}
}

?>