<?php
	include 'includes/functions.php';
	
	$salt = "60b535c5";
	$randomLength = strlen($salt);
	$password = "bombalurina";
	$md5 = md5($password . $salt);
	
	echo "Salt: " . $salt . "<br/>";
	echo "Password: " . $password . "<br/>";
	echo "MD5: " . $md5 . "<br/>";
	echo "MD5 length: " . strlen($md5);
	
	$allemails = queryMySQL("SELECT email FROM jobdash_users");
	
	while($row = mysql_fetch_array($allemails)) {
		echo $row['email'] . "; ";
	}
?>