<?php
	session_start();
	require_once('functions.php');
	
	$projID = $_GET['pid'];
	$regionNameID = $_GET['regionID'];
	$milestones = queryMySQL("SELECT * FROM milestones WHERE projectID = '$projID' ORDER BY milestone ASC");
	$msList = "";
	$msDescList = "";
	while ($row = mysql_fetch_array($milestones)) {
		list($year, $month, $day) = explode("-", $row['milestone']);
		$msList .= $day . "/" . $month . "/" . $year . "<br />";
		
		$msDescID = $row['milestone_typeID'];
		$msDesc = mysql_fetch_array(queryMySQL("SELECT name FROM milestone_types WHERE id='$msDescID'"));
		$msDescList .= $msDesc['name'] . "<br />";
	}
	
	$tasks = queryMySQL("SELECT * FROM tasks WHERE projectID = '$projID' LIMIT 3");
	$taskList = "";
	$i = 1;
	while ($row = mysql_fetch_array($tasks)) {
		if (fmod($i,2) > 0) {
			$rowType = "odd";
		} else {
			$rowType = "even";
		}
		$taskID = $row['id'];
		$statusID = $row['statusID'];
		$userID = $row['userID'];
		$updateUserID = $row['lastUpdateBy'];
		$updateDate = $row['lastUpdateOn'];
		$taskName = $row['name'];
		//Get status
		$taskStatus = queryArray("SELECT name FROM task_status WHERE id='$statusID'"); 
		
		//Get resource name 
		$resource = queryArray("SELECT * FROM users WHERE id='$userID'"); 
		$resourceTitleID = $resource['titleID'];
		//Get resource title
		$resourceTitle = queryArray("SELECT abbr FROM user_titles WHERE id='$resourceTitleID'");
		
		//Get the latest comment
		$comment = queryArray("SELECT comment FROM task_comments WHERE taskID='$taskID' ORDER BY lastUpdateOn DESC LIMIT 1");
		
		//Format update string Date Time by Resource
		$updateUser = queryArray("SELECT firstname FROM users WHERE id='$updateUserID'");
		list($date, $time) = explode(" ", $updateDate);
		list($year, $month, $day) = explode("-", $date);
		list($hour, $minute, $second) = explode(":", $time);
		$update = $day."/".$month."/".$year." @ ".$hour.":".$minute." by ".$updateUser['firstname'];
		
		$taskList .= "<tr class=\"$rowType\">";
		$taskList .= "<td>".$taskName."</td>";
		$taskList .= "<td>".$resource['firstname'] . " " . $resource['lastname']."</td>";
		$taskList .= "<td>".$resourceTitle['abbr']."</td>";
		$taskList .= "<td>".$taskStatus['name']."</td>";
		$taskList .= "<td>".$comment['comment']."</td>";
		$taskList .= "<td>".$update."</td>";
		$taskList .= "</tr>";
		$i++;
	}
	
?>

<table width="100%" cellpadding="0" cellspacing="0" border="0" class="detailsSub">
	<tr>
  	<td width="574"></td>
    <td width="96" valign="top"><?php echo $msList; ?></td>
    <td width="174" valign="top"><?php echo $msDescList; ?></td>
    <td wdith="24" align="center" valign="top"><a href="project/edit/<?php echo $projID;?>"><img src="images/icon_info.png" width="16" height="16" /></a><?php if ($_SESSION['regionID'] == $regionNameID) { ?><img src="images/icon_delete.png" width="16" height="16" /><?php } ?></td>
  </tr>
  <tr>
  	<td colspan="4">
    	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="taskDetails">
      	<thead>
          <tr>
						<th colspan="6" class="divider">Tasks</th>
          </tr>
          <tr>
						<th>Name</th>
            <th>Resource</th>
            <th>Title</th>
            <th>Status</th>
            <th>Comment</th>
            <th>Last Updated</th>          
          </tr>
        </thead>
        <tbody>
					<?php echo $taskList; ?>
        </tbody>
        <tfoot>
        	<tr>
          	<th colspan="6"><a href="project/edit/<?php echo $projID;?>">More Info</a></th>
          </tr>
        </foot>
      </table>
    </td>
  </tr>

</table>