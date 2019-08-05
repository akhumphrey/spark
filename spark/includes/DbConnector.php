<?php

/*

	Class: DbConnector
	Purpoes: Connect to, query, retrieve data from, and close the MySQL database
	Revision: 1

*/

require_once('SystemComponent.php');

class DbConnector extends SystemComponent {

	protected $theQuery;
	protected $link;
	public $dbTables;

	//	Function: __construct, Purpose: define database connection parameters
	public function __construct(){

		//	Load settings from parent class
		$settings = parent::getSettings();

		//	Get the main settings from the array we just loaded
		$host	= $settings['dbhost'];
		$db	= $settings['dbname'];
		$user	= $settings['dbusername'];
		$pass	= $settings['dbpassword'];
		$this->dbTables = $settings['dbtables'];

		//	Free up the $settings array
		unset($settings);

		//	Connect to the database server
		$this->link = mysql_connect($host, $user, $pass);

		//	Select the correct database
		mysql_select_db($db);

		// Close the database link after each request
		register_shutdown_function(array(&$this, 'close'));

	}


	//	Function: query, Purpose: Execute a database query
	public function query($query){

		$this->theQuery = $query;
		return mysql_query($query, $this->link);

	}


	// Function: getQuery, Purpose: Returns the last database query, for debugging
	public function getQuery(){

		return $this->theQuery;

	}


	// Function: getNumRows, Purpose: Return row count
	public function getNumRows($result){

		return mysql_num_rows($result);

	}


	//	Function: fetchArray, Purpose: Get array of query results
	public function fetchArray($result){

		return mysql_fetch_array($result);

	}


	//	Function: fetchObject, Purpose: Get object of query results
	public function fetchObject($result){

		return mysql_fetch_object($result);

	}


	//	Function: close, Purpose: Close the connection
	public function close(){

		mysql_close($this->link);

	}

}

?>