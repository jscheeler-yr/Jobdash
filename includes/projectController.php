<?php
	session_start();
	require_once('functions.php');
	
	if (isset($_GET['action'])) {
		switch ($_GET['action']) {
			case 'add':
				$uid = $_SESSION['user'];
				$projectName = addslashes(sanitizeString($_POST['name']));
				$projectType = $_POST['type'];
				$jobNum = sanitizeString($_POST['jobNum']);
				
				$addResult = queryMySQL("INSERT INTO projects (name, jobNumber, typeID, owner_userID) VALUES('$projectName', '$jobNum', '$projectType', '$uid')");
																 
				//$addResult = true;
				if (!addResult) {
					//header("Location: /admin/user/add/fail");
					echo "fail";
				} else {
					//If successful, get the id of the project, then continue to add milestones and tasks
					$projectID = mysql_fetch_array(queryMySQL("SELECT id FROM  projects WHERE name='$projectName' AND jobNumber='$jobNum' AND typeID='$projectType' AND owner_userID='$uid' ORDER BY id DESC LIMIT 1"));
										//Get milestone dates
					$pid = $projectID['id'];										
					$milestones = $_POST['msDate'];
					$countMS = count($milestones);
					$milestoneType = $_POST['msType'];
					
					$msValues = "";
					//Create values statements
					foreach ($milestones as $key=>$ms) {
						$msValues .= "('$ms', '".$milestoneType[$key]."' ,'$pid')";
						if ($key < $countMS-1) {
							$msValues .= ", ";
						}
					}
					$msQuery = "INSERT INTO milestones (milestone, milestone_typeID, projectID) VALUES $msValues";
					//echo $msQuery;
					//Insert milestones into table
					queryMySQL($msQuery);
					
					//Tasks
					$taskNames = $_POST['taskName'];
					$countTasks = count($taskNames);
					$taskResource = $_POST['resource'];

					$taskValues = "";
					//Create values statements
					foreach ($taskNames as $key=>$task) {
						$task = addslashes($task);
						$taskValues .= "('$task', '".$taskResource[$key]."' ,'$pid')";
						if ($key < $countTasks-1) {
							$taskValues .= ", ";
						}
					}
					
					//Insert milestones into table
					queryMySQL("INSERT INTO tasks (name, userID, projectID) VALUES $taskValues");
					
					header("Location: " . DOC_ROOT);
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