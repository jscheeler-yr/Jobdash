<?php
	include 'header.php';
	
	if ($loggedIn) { 
		include 'includes/viewBy'.ucfirst($view).'.php';
	}
	require_once 'footer.php';
?>
