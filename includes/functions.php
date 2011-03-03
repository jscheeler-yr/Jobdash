<?php //includes/functions.php

	define("DBHOST", "localhost");
	define("DBUSER", "root");
	define("DBPASS", "");
	define("DBNAME", "jobdash");
	define("APPNAME", "Jobdash v2.0");
	define("USERTABLE", "users");
	define("DOC_ROOT", "/Jobdash/");
/*
	define("DBHOST", "mysql50-53.wc2.dfw1.stabletransit.com");
	define("DBUSER", "499656_admin");
	define("DBPASS", "6d1RYpDOc0");
	define("DBNAME", "499656_area601");
	define("APPNAME", "Jobdash v2.0");
	define("USERTABLE", "jobdash_users");
*/	
	$connect = connect();
	//Function to open database connection
	function connect() {
		$connect = mysql_connect(DBHOST, DBUSER, DBPASS, DBNAME);
		mysql_select_db(DBNAME, $connect);
		
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
	
	//login function
	function login($email, $password) {
		$saltResult = mysql_query("SELECT salt FROM ". USERTABLE ." WHERE email='$email' LIMIT 1");
		$row = mysql_fetch_array($saltResult);
		if (!$row) {
			return false;
		} else {
			$salt = $row['salt'];
			$hash = md5($password . $salt);
			$checkUser = queryMySQL("SELECT id FROM ". USERTABLE ."  WHERE email='$email' AND password='$hash' LIMIT 1");
			$userID = mysql_fetch_array($checkUser);

			if (!$userID) {
				return false;
			} else {				
				return $userID['id'];
			}
		}		
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
		$result = queryMySQL("SELECT * FROM ". USERTABLE ." WHERE id='$user'");
		
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_array($result);
			return $row['permissionsID'];
		}
	}	
?>