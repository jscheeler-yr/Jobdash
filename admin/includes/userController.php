<?php
	require_once('../../includes/functions.php');
	
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {
			case 'add':
				$firstname = sanitizeString($_POST['firstname']);
				$lastname = sanitizeString($_POST['lastname']);
				$departmentID = $_POST['department'];
				$titleID = $_POST['title'];
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
					//header("Location: /admin/user/add/success");
					echo "success";
				}								
				break;
			case 'edit':
				break;
		}
	}
?>