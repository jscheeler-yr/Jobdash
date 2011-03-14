<?php
	include 'includes/functions.php';
	session_start();
	
	if (isset($_SESSION['user']) || isset($_GET['user'])) {
		$user = (isset($_SESSION['user'])?$_SESSION['user']:$_GET['user']);
		$_SESSION['user'] = $user;
		$loggedIn = true;
		$userInfo = userInfo($user);
		$user_perm = $userInfo['permissionsID'];
		$regionID = $userInfo['regionID'];
	} else {
		$loggedIn = false;
	}

?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="shortcut icon" href="<?php echo DOC_ROOT; ?>images/favicon.ico" />
<title><?php echo APPNAME; ?></title>
<script src="<?php echo DOC_ROOT; ?>js/jquery-1.5.min.js"></script>
<script src="<?php echo DOC_ROOT; ?>js/jquery-ui-1.8.min.js"></script>
<script src="<?php echo DOC_ROOT; ?>js/jobdash.js"></script>
<script src="<?php echo DOC_ROOT; ?>js/jquery.tablesorter.js"></script>
<script src="<?php echo DOC_ROOT; ?>js/jquery.tablesorter.pager.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="stylesheet" href="<?php echo DOC_ROOT; ?>css/main.css" media="screen" />
<link rel="stylesheet" href="<?php echo DOC_ROOT; ?>css/table.css" media="screen" />
<link rel="stylesheet" href="<?php echo DOC_ROOT; ?>css/redmond/jquery-ui-1.8.css" media="screen" />
</head>

<body>
<div id="container">
<div id="header" class="filmStrip paddingT10">
  <header class="container">
		<div id="title" title="JOBDASH | v2.0"><span class="hidden">JOBDASH | v2.0</span></div> 
<?php	
	if (!$loggedIn) {
		require_once('includes/login.php');
	} else {
		//load user header
		require_once('includes/user.php');
		require_once('includes/tabs.php');
		require_once('includes/userNav.php');	
	}
	
	
	
?>
  </header>
</div>
<div id="body">
	<div class="container">