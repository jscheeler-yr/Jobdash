		<div id="userInfo" class="headerTopRight">
<?php
	// Get user information.  Picture, First/Last names, title
	$userInfo = mysql_fetch_array(queryMySQL("SELECT firstname, lastname, picture, titleID FROM " . USERTABLE . " WHERE id='$user' LIMIT 1"));
	$userName = ucfirst($userInfo['firstname']) . " " . ucfirst($userInfo['lastname']);
	$userTitleID = $userInfo['titleID'];
	$userTitle = mysql_fetch_array(queryMySQL("SELECT full FROM user_titles WHERE id='$userTitleID' LIMIT 1"));
	echo '<div id="userImg"><a href="myAccount?user='. $user .'"><img src="'. DOC_ROOT .'userImg/40x40/' . $userInfo['picture'] . '" width="40" height="40" alt="'. $userName .'" /></a></div>';
	echo '<div id="userNameTitle" class="trebuchet">';
	echo '	<p class="font18">'. $userName .'</p>';
	echo '	<p class="font12">'. $userTitle['full'] .'</p>';
	echo '</div>';
      
?>
		</div>