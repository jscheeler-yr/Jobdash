<?php
	include '../includes/functions.php';
	session_start();
	
	if (isset($_SESSION['user']) || isset($_GET['user'])) {
		$user = (isset($_SESSION['user'])?$_SESSION['user']:$_GET['user']);
		$_SESSION['user'] = $user;
		$loggedIn = true;
		$user_perm = userLevel($user);
	} else {
		$loggedIn = false;
	}
	
	include '../header.php';
?>
<div id="bodyContainer">

</div>

<?php
	require_once '../footer.php';
?>

</body>
</html>