<?php
	require_once('../../includes/functions.php');
	
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {
			case 'add':
				$firstname = sanitizeString($_POST['firstname']);
				$lastname = sanitizeString($_POST['lastname']);
				$departmentID = $_POST['department'];
				$titleID = $_POST['selectTitle'];
				$phone = sanitizeString($_POST['phone']);
				$email = sanitizeString($_POST['email']);
				$regionID = $_POST['region'];
				$permissionsID = $_POST['permissions'];
				
				$password = generatePassword();
				$salt = generateSalt();
				
				$md5 = md5($password . $salt);
				
				$addResult = queryMySQL("INSERT INTO ". USERTABLE ." (firstname, lastname, email, departmentID, titleID, phone, regionID, permissionsID, password, salt) 
																 VALUES('$firstname', '$lastname', '$email', '$departmentID', '$titleID', '$phone', '$regionID', '$permissionsID', '$md5', '$salt')");
				if (!$addResult) {
					//header("Location: /admin/user/add/fail");
					echo "fail";
				} else {
					//If successful, send user an email to the user to let them know they are signed up and what their password is.
					$uid = mysql_fetch_array(queryMySQL("SELECT id FROM  ". USERTABLE ." WHERE email = '$email' ORDER BY id DESC LIMIT 1"));
					
					newUserEmail($uid['id'], $password);
					
					header("Location: success/" . $uid['id']);
					//echo "success";
					//echo "<br> $password";
				}								
				break;
			case 'edit':
				$uid = $_GET['uid'];
				//Get current information
				$uInfo = mysql_fetch_array(queryMySQL("SELECT * FROM ".USERTABLE." WHERE id='$uid' LIMIT 1"));
				if (isset($_GET['getPass'])) {
					$password = $_GET['pass'];
					$uPass = $uInfo['password'];
					$uSalt = $uInfo['salt'];
					
					if (md5($password.$uSalt) == $uPass) {
						echo "true";
					} else {
						echo "false";
					}
					
				} else {
					$firstname = sanitizeString($_POST['firstname']);
					$lastname = sanitizeString($_POST['lastname']);
					$departmentID = $_POST['department'];
					$titleID = $_POST['selectTitle'];
					$phone = sanitizeString($_POST['phone']);
					
					if ($referrer == 'admin') {
						$regionID = $_POST['region'];
						$permissionsID = $_POST['permissions'];
					} else {
						$regionID = $uInfo['regionID'];
						$permissionsID = $uInfo['permissionsID'];
					}
					
					//Password.  If currentpassword has a value, update password, else, leave it alone
					if ($_POST['currentPassword'] != "") {
						$password = sanitizeString($_POST['password']);
						$salt = $uInfo['salt'];
						$md5 = md5($password . $salt);
					} else {
						$md5 = $uInfo['password'];
					}
					
					
					$updateResult = queryMySQL("UPDATE ". USERTABLE ." SET firstname='$firstname', lastname='$lastname', departmentID='$departmentID', titleID='$titleID', phone='$phone', regionID='$regionID', permissionsID='$permissionsID', password='$md5' WHERE id='$uid'");
					
					if (!$updateResult) {
						//header("Location: /admin/user/add/fail");
					} else {
						header("Location: success/".$uid['id']);
					}								
				}
				break;
			case 'delete':
				$uid = $_GET['id'];
				$user = mysql_fetch_array(queryMySQL("SELECT firstname, lastname FROM  ". USERTABLE ." WHERE id = '$uid' LIMIT 1"));
				if (isset($_GET['getName'])) {
					echo $user['firstname'] . " " . $user['lastname'];
				} else {
					$delete = queryMySQL("DELETE FROM ". USERTABLE ." WHERE id = '$uid' LIMIT 1");
					if ($delete) {
						$userName = $user['firstname'] . "%20" . $user['lastname'];
						header("Location: success/$userName");
					} else {
						header("Location: fail");
					}
				}
				break;
		}
	}
?>