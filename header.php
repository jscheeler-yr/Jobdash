<?php //header.php
	
	include 'includes/functions.php';
	session_start();
	
	if (isset($_SESSION['user']) || isset($_GET['user'])) {
		$user = (isset($_SESSION['user'])?$_SESSION['user']:$_GET['user']);
		$_SESSION['user'] = $user;
		$loggedIn = true;
		$user_perm = userLevel($user);
	} else {
		$loggedIn = false;
	}
	
	
?>

<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo APPNAME; ?></title>
<script src="js/jquery-1.5.min.js"></script>
<script src="js/jobdash.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" href="css/main.css" media="screen" />
</head>

<body>
<div id="header">
  <header>
		<div id="title" title="JOBDASH | v2.0"><span class="hidden">JOBDASH | v2.0</span></div> 
<?php	
	if (!$loggedIn) {
		include('includes/login.php');
	} else {
		//load user header
		echo "Hello :)";
	}
?>
  </header>
</div>
