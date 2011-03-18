<?php //includes/functions.php
	include('Mail.php');
	include('Mail/mime.php');
	define("DBHOST", "localhost");
	define("DBUSER", "root");
	define("DBPASS", "");
	define("DBNAME", "jobdash");
	
	
	/*define("DBHOST", "mysql50-81.wc2.dfw1.stabletransit.com");
	define("DBUSER", "499656_jobdash");
	define("DBPASS", "Passw0rd");
	define("DBNAME", "499656_jobdash");*/
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
			echo mysql_error();
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
	
	function queryArray($query) {
		$result = queryMySQL($query);
		if (!$result) {
			return false;
		} else {
			return mysql_fetch_array($result);
		}
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
	function userInfo($user) {
		$result = queryMySQL("SELECT * FROM ". USERTABLE ." WHERE id='$user'");
		
		if (mysql_num_rows($result)) {
			$row = mysql_fetch_array($result);
			return $row;
		}
	}	
	
function generateSalt() {
	$length = 8;
	$characters = '023456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$characters .= 'abcdefghijkmnopqrstuvwxyz!@#';
	$string = "";
	for ($p = 0; $p <= $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters))];
	}
	return $string;
}

function generatePassword ($length = 8) {
	$password = "";
	
	// define possible characters - any character in this string can be
	// picked for use in the password, so if you want to put vowels back in
	// or add special characters such as exclamation marks, this is where
	// you should do it
	$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
	
	// we refer to the length of $possible a few times, so let's grab it now
	$maxlength = strlen($possible);
	
	// check for length overflow and truncate if necessary
	if ($length > $maxlength) {
		$length = $maxlength;
	}
	
	// set up a counter for how many characters are in the password so far
	$i = 0; 
	
	// add random characters to $password until $length is reached
	while ($i < $length) { 
	
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, $maxlength-1), 1);
			
		// have we already used this character in $password?
		if (!strstr($password, $char)) { 
			// no, so it's OK to add it onto the end of whatever we've already got...
			$password .= $char;
			// ... and increase the counter by one
			$i++;
		}
	
	}
	
	// done!
	return $password;
	
}

function newUserEmail($uid, $password) {
	$userInfo = mysql_fetch_array(queryMySQL("SELECT * FROM " .USERTABLE." WHERE id='$uid' LIMIT 1"));
	$recipient = $userInfo['email'];
	$name = $userInfo['firstname'] . " " .$userInfo['lastname'];
	
	echo $email;
	
	$from = "jobdash@yr.com";
	$subject = "Welcome to Jobdash";
	$html = <<<html
	<html><body>
	<h3>Welcome to Jobdash</h3>
	<p>
	Dear $name,<br />
	An account has been created for you on Jobdash. To start using the application go to <a href="http://jobdash.area601.net" target="_blank">jobdash.area601.net</a> and download the application.  The link to download Jobdash can be found at the bottom right of the page.</p>
	<p>Please contact your traffic person or project manager if you have any questions.</p>
	<p>Here are your log in credentials.  <b>Please do not misplace this</b></p>
	<p>Username: $recipient</p>
	<p>Password: $password</p>
	<p>The Jobdash Team</p>
html;
	
	$headers['From'] = $from;
	$headers['Subject'] = $subject;
	// Instantiate Mail_mime Class
	$mimemail = new Mail_mime();
	// Set HTML Message
	$mimemail->setHTMLBody($html);
	// Build Message
	$message = $mimemail->get();
	// Prepare the Headers
	$mailheaders = $mimemail->headers($headers);
	// Create New Instance of Mail Class
	$email =& Mail::factory('mail');
	// Send the E-mail Already!
	$email->send($recipient, $mailheaders, $message) or die("Can't send message!");

}

?>