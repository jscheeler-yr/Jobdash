      	<h4 class="nomargin">Select User:</h4>
      	<table width="100%" cellpadding="1" cellspacing="0" class="tablesorter">
        	<thead>
            <tr>
              <th class="noBorder"></th>
              <th>Resource</th>
              <th>Department</th>
              <th>Title</th>
              <th>Last Log In</th>
            </tr>
					</thead>
          <tbody>
<?php
	require_once('../../includes/functions.php');
	//Select all users in the selected region. 
	$regionID = $_GET['region'];
	$usersArr = queryMySQL("SELECT * FROM ". USERTABLE ." WHERE regionID='$regionID'");
	while($users=mysql_fetch_array($usersArr)) {
		//Get department name
		$deptID = $users['departmentID'];
		$dept = mysql_fetch_array(queryMySQL("SELECT * FROM departments WHERE id='$deptID' LIMIT 1"));
		//Get title
		$titleID = $users['titleID'];
		$title = mysql_fetch_array(queryMySQL("SELECT * FROM user_titles WHERE id='$titleID' LIMIT 1"));
		
		//format the date
		list($date, $time) = explode(" ", $users['lastLoggedIn']);
		list($year, $month, $day) = explode("-", $date);
		list($hour, $minute, $seconds) = explode(":", $time);
		
		$lastLoggedIn = date('n/j/Y @ g:ia', mktime($hour, $minute, $seconds, $month, $day, $year));
?>
            <tr>
              <td class="noBorder"><input type="radio" id="delete" value="1" /></td>
              <td><?php echo $users['lastname'] .", " . $users['firstname']; ?></td>
              <td><?php echo $dept['name']; ?></td>
              <td><?php echo $title['full']; ?></td>
              <td><?php echo $lastLoggedIn; ?></td>
            </tr>
<?php
	}
?>
          </tbody>
        </table>
        <div class="right">
        	<a class="submit orange medium">Cancel</a>
          <a class="submit red medium">Delete</a>
          <a class="submit green medium">Next</a>
        </div>
