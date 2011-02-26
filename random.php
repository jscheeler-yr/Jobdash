<?php

	$salt = "60b535c5";
	$randomLength = strlen($salt);
	$password = "bombalurina";
	$md5 = md5($password . $salt);
	
	echo "Salt: " . $salt . "<br/>";
	echo "Password: " . $password . "<br/>";
	echo "MD5: " . $md5 . "<br/>";
	echo "MD5 length: " . strlen($md5);
	
	
	
?>