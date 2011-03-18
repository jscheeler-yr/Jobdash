<?php
	session_start();
	require_once('functions.php');
	
	$uid = $_GET['uid'];
	$tasks = queryMySQL("SELECT * FROM tasks WHERE userID = '$uid'");
	$taskList = "";
	$i = 1;
	while ($row = mysql_fetch_array($tasks)) {
		if (fmod($i,2) > 0) {
			$rowType = "odd";
		} else {
			$rowType = "even";
		}

		$taskID = $row['id'];
		$updateDate = $tasks['lastUpdateOn'];
		$updateUserID = $tasks['lastUpdateBy'];

		//Get task name.
		
		$taskName = $row['name'];
		
		//Get task project
		$projectID = $row['projectID'];
		$project = queryArray("SELECT name, typeID, milestone FROM projects WHERE id='$projectID' LIMIT 1");
		
		$projectName = $project['name'];
		
		$projectName = (strlen($projectName) > 30)?substr($projectName, 0, 30) . "..." : $projectName;
		
		$typeID = $project['typeID'];
		$milestone = $project['milestone'];
		
		//Get type name
		$type = queryArray("SELECT name FROM project_types WHERE id='$typeID' LIMIT 1");
		$typeName = $type['name'];
		
		//Get the status name
		$statusID = $row['statusID'];
		$status = queryArray("SELECT name FROM task_status WHERE id='$statusID' LIMIT 1");
		$statusName = $status['name'];
		
		//Get the latest comment
		$comment = queryArray("SELECT * FROM task_comments WHERE taskID='$taskID' ORDER BY lastUpdateOn DESC LIMIT 1");
		$commentNewest = "";
		if ($comment != false) {
			$commentNewest .= $comment['comment'];
		}
		
		//last task update on
		$updateUser = queryArray("SELECT firstname FROM users WHERE id='$updateUserID' LIMIT 1");
		$update ="";
		if ($updateDate != NULL) {
			list($date, $time) = explode(" ", $updateDate);
			list($year, $month, $day) = explode("-", $date);
			list($hour, $minute, $second) = explode(":", $time);
			$update .= $day."/".$month."/".$year." @ ".$hour.":".$minute." by ".$updateUser['firstname'];
		}
		
		list($year, $month, $day) = explode("-", $milestone);
		$milestone = $day . "/" . $month . "/" . $year . "<br />";
		$taskList .= "<tr class=\"$rowType\">";
		$taskList .= "<td>".$i."</td>";
		$taskList .= "<td>".$taskName."</td>";
		$taskList .= "<td><a href=\"project/edit/".$projectID."\">".$projectName."</a></td>";
		$taskList .= "<td>".$typeName."</td>";
		$taskList .= "<td>".$milestone."</td>";
		$taskList .= "<td>".$statusName."</td>";
		$taskList .= "<td>".$commentNewest."</td>";
		$taskList .= "<td>".$update."</td>";
		$taskList .= "</tr>";
		
		$i++;
	}
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="detailsSub">
  <tr>
  	<td colspan="4">
    	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="taskDetails">
      	<thead>
          <tr>
						<th>Task</th>
            <th>Name</th>
            <th>Project</th>
            <th>Type</th>
            <th>MS</th>
            <th>Status</th>
            <th>Comments</th>
            <th>Last Update</th>
          </tr>
        </thead>
        <tbody>
					<?php echo $taskList; ?>
        </tbody>
        <tfoot>
        	<tr><th colspan="8"></th></tr>
        </tfoot>
      </table>
    </td>
  </tr>

</table>