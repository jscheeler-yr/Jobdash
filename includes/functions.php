<?php //includes/functions.php

	define("DBHOST", "localhost");
	define("DBUSER", "root");
	define("DBPASS", "");
	define("DBNAME", "jobdash");
	define("APPNAME", "Jobdash v2.0");
	define("USERTABLE", "users");
	
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
		$saltResult = queryMySQL("SELECT salt FROM ". USERTABLE ." WHERE email='$email' LIMIT 1");
		
		if (!$saltResult) {
			return false;
		} else {
			$row = mysql_fetch_array($saltResult);
			$salt = $row['salt'];
			
			$hash = md5($password . $salt);
			$checkUser = queryMySQL("SELECT id FROM ". USERTABLE ." WHERE email='$email' AND password='$hash' LIMIT 1");
			$row = mysql_fetch_array($checkUser);
			
			if (count($row) != 1) {
				return false;
			} else {				
				return $row['id'];
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