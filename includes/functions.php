<?php //includes/functions.php

	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "jobdash";
	$appname = "Jobdash v2.0";
	
	//Function to open database connection
	function connect() {
		$connect = mysql_connect($db_host, $dbuser, $dbpass, $dbname);
		
		if (!$connect) {
			header("Location: error");
		} else {
			return $connect;
		}
	}
	
	//Function to close database connection
	function disconnectDB($connection) {
		mysql_close($connection);
	}	
	
	//query database
	function queryMySQL($query) {
		$result = mysql_query($query) or die(mysql_error());
		return $result;
	}
	
	//log out session destroy
	function destroySession() {
		$_SESSION = array();
		
		if (session_id() != "" || isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time()-2592000, '/');
			
		session_destroy();
	}
	
	//Sanitize a string inserted through any input method
	function sanitizeString($var) {
		$var = strip_tags($var);
		$var = htmlentities($var);
		$var = stripslashes($var);
		return mysql_real_escape_string($var);
	}
	
	//Return user permission levels
	function userLevel($user) {
		$result = queryMySQL("SELECT * FROM $userTable WHERE id='$user'");
		
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_array($result);
			return $row['permissionsID'];
		}
	}	
?>