<?php
	session_start();
	require_once('functions.php');
	
	$rid = $_SESSION['regionID'];
	//Get the name of the region to return to.
	$regionSQL = queryArray("SELECT name FROM region WHERE id='$rid'");
	$region = strtolower(str_replace(" ", "", $regionSQL['name']));
	
	if (isset($_GET['action'])) {
		$uid = $_SESSION['user'];
		$date = date('Y-m-d h:i:s');
		switch ($_GET['action']) {
			case 'add':				
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
					
					//Get the earliest milestone
					$msQuery = "SELECT milestone FROM milestones WHERE projectID = '$pid' ORDER BY milestone ASC";
					$ascMS = mysql_fetch_array(queryMySQL($msQuery));
					//Update the project with the most impending milestone
					$soonestMS = $ascMS['milestone'];
					queryMySQL("UPDATE projects SET milestone = '$soonestMS' WHERE id='$pid'");
					
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
					
					header("Location: " . DOC_ROOT . $region);
				}		
				
				break;
			case 'edit':
				$pid = $_GET['pid'];
				$projectName = addslashes(sanitizeString($_POST['name']));
				$projectType = $_POST['type'];
				$jobNum = sanitizeString($_POST['jobNum']);
				
				$updateResult = queryMySQL("UPDATE projects SET name='$projectName', typeID='$projectType', jobNumber='$jobNum' WHERE id='$pid'");
				//$updateResult = true;											 
				//$addResult = true;
				if (!$updateResult) {
					//header("Location: /admin/user/add/fail");
					echo "fail";
				} else {
					//See if any milestones were deleted
					if (isset($_POST['removeMS'])) {
						//If yes get the removeMS post value
						$removeMS = $_POST['removeMS'];
						$removeSQL = "";
						//Loop through array and write Delete statement
						foreach($removeMS as $value) {
							queryMySQL("DELETE FROM milestones WHERE id='$value'");
						}
					}
					
					//Determine which Milestones were updated
					$milestonesID = $_POST['msID'];
					$milestones = $_POST['msDate'];
					$milestoneType = $_POST['msType'];
					
					//Array counts
					$countMSOld = count($milestonesID);
					$countMSTotal = count($milestones);
					
					$msUpdate = "";
					//Create values statements
					foreach ($milestonesID as $key=>$msID) {
						$ms = $milestones[$key];
						$msType = $milestoneType[$key];
						queryMySQL("UPDATE milestones SET milestone='$ms', milestone_typeID='$msType' WHERE id='$msID'");
						//echo "UPDATE milestones SET milestone='$ms', milestone_typeID='$msType' WHERE id='$msID'";
					}
					
					//echo $countMSOld < $countMSTotal;
					//Now we check to see if there are new milestones by checking to see if the number of old IDs passed is less than the number of total dates passed. 
					if ($countMSOld < $countMSTotal) {
					//Now we add the new milestones by only grabbing the milestones in the $milestones array after the old values
						$msValues = "";
						//Create values statements
						foreach ($milestones as $key=>$ms) {
							if ($key >= $countMSOld) {
								$msValues .= "('$ms', '".$milestoneType[$key]."' ,'$pid')";
								if ($key < $countMSTotal-1) {
									$msValues .= ", ";
								}
							}
						}
						$msQuery = "INSERT INTO milestones (milestone, milestone_typeID, projectID) VALUES $msValues";
						//echo $msQuery;
						//Insert milestones into table
						queryMySQL($msQuery);
					}
					
					//Get the earliest milestone
					$msQuery = "SELECT milestone FROM milestones WHERE projectID = '$pid' ORDER BY milestone ASC";
					$ascMS = mysql_fetch_array(queryMySQL($msQuery));
					//Update the project with the most impending milestone
					$soonestMS = $ascMS['milestone'];
					queryMySQL("UPDATE projects SET milestone = '$soonestMS' WHERE id='$pid'");

					//See if any tasks were removed
					if (isset($_POST['removeTask'])) {
						//If yes get the removeTask post value
						$removeTask = $_POST['removeTask'];
						$removeSQL = "";
						//Loop through array and write Delete statement
						foreach($removeTask as $value) {
							queryMySQL("DELETE FROM tasks WHERE id='$value'");
						}
					}
										
					//Determine which Tasks were updated
					$taskID = $_POST['taskID'];
					$tasks = $_POST['taskName'];
					$taskResource = $_POST['resource'];
					$taskStatus = $_POST['status'];
					
					$countTaskOld = count($taskID);
					$countTaskTotal = count($tasks);
					
					$taskUpdate = "";
					//Create values statements
					foreach ($taskID as $key=>$tID) {
						$taskName = $tasks[$key];
						$resource = $taskResource[$key];
						$status = $taskStatus[$key];
						queryMySQL("UPDATE tasks SET name='$taskName', userID='$resource', statusID='$status', lastUpdateBy='$uid', lastUpdateOn='$date' WHERE id='$tID'");
					}

					$taskValues = "";
					//Now we check to see if there are new tasks by checking to see if the number of old IDs passed is less than the number of total task names passed. 
					if ($countTaskOld < $countTaskTotal) {
					//Now we add the new tasks by only grabbing the tasks in the $task array after the old values
						$taskValues = "";
						//Create values statements
						foreach ($tasks as $key=>$task) {
							if ($key >= $countMSOld) {
								$task = addslashes($task);
								$taskValues .= "('$task', '".$taskResource[$key]."' ,'$pid', '$date', '$uid')";
								if ($key < $countTaskTotal-1) {
									$taskValues .= ", ";
								}
							}
						}
						$taskQuery = "INSERT INTO tasks (name, userID, projectID, lastUpdateOn, lastUpdateBy) VALUES $taskValues";
						//Insert tasks into table
						queryMySQL($taskQuery);
					}

					
					header("Location: " . DOC_ROOT . $region);
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