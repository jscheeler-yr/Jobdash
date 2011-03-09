<?php
	include '../header.php';
	/*include '../includes/functions.php';
	session_start();
	
	if (isset($_SESSION['user']) || isset($_GET['user'])) {
		$user = (isset($_SESSION['user'])?$_SESSION['user']:$_GET['user']);
		$_SESSION['user'] = $user;
		$loggedIn = true;
		$userInfo = userInfo($user);
		$user_perm = $userInfo['permissionsID'];
		$userRegion = $userInfo['regionID'];
	} else {
		$loggedIn = false;
	}
	*/
	//Generate options for the region array.
	$regions = queryMySQL("SELECT * FROM region");
	$regionsHTML = "";
	while($row=mysql_fetch_array($regions)) {
		//If the user only has regional accession, permissions level 2 only allow them to choose their region
		if (($user_perm == 2) && ($row['id'] != $userRegion)) {
			$disabled = 'disabled="disabled"';
		} else {
			$disabled = "";
		}
		$regionsHTML .= '<option value="'.$row['id'].'" '. $disabled .'>'.$row['name'].'</option>';
	}
	
?>
    <div id="admin_action" class="divider">
      <h3>What would you like to do?</h3>
      <div class="row1"><input type="radio" name="action" value="new_user" /> <span>Add a new user</span></div>
      <div class="row2"><input type="radio" name="action" value="edit" /> <span>Update a user</span></div>
    </div>
    <div id="chooseUser">
    	<h4 class="left nomargin">Select Region:</h4>
      <div class="paddingL10">
      	<select id="region">
        	<option value="blank">-- Select --</option>
          <?php echo $regionsHTML;?>
        </select>
      </div>
      <div id="usersTable" class="paddingT10">
      </div>
    </div>

<?php
	require_once '../footer.php';
?>
<script type="text/javascript">
$(document).ready(function() {  
	//Hide select region section
	$('#chooseUser').hide(); 
	 
	// call the tablesorter plugin 
	$("table").tablesorter({
		widgets: ['zebra'],
		headers: { 0:{sorter: false}}
	}); 
	
	/* --- User actions --- */
	//After user selects what they would like to do
	$("input[name='action']").change(function() {
		if ($("input[name='action']:checked").val() == 'new_user') {
			window.location = 'user/add';
		} else {
			$('#chooseUser').show(); 
		}
	});
	
	//If the user selects 'Update a user'
	$('select').change(function() {
			if ($('select option:selected').val() != "blank") {
				$.get('includes/chooseUser.php', {region: $('select option:selected').val()}, function(html) {
					$('#usersTable').html(html);
				});
			}
	});
});
</script>

</body>
</html>