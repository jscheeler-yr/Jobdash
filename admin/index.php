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
	
	if(isset($_GET['action'])){
		$showMessage = "show";
		switch ($_GET['action']) {
			case 'userAdded':
				$uid = $_GET['uid'];
				$userAdded = mysql_fetch_array(queryMySQL("SELECT firstname, lastname FROM ". USERTABLE ." WHERE id = '$uid' LIMIT 1"));
				$message = "You have successfully added user " . $userAdded['firstname'] . " " . $userAdded['lastname'];
				break;
			case 'userDeleted':
				$message = "You have successfully deleted " . $_GET['name'];
				break;
			case 'userUpdated':
				$uid = $_GET['uid'];
				$userUpdated = mysql_fetch_array(queryMySQL("SELECT firstname, lastname FROM ". USERTABLE ." WHERE id = '$uid' LIMIT 1"));
				$message = "You have successfully updated information for user " . $userUpdated['firstname'] . " " . $userUpdated['lastname'];
				break;
		}
	} else {
		$showMessage = "hide";
		$message ="";
	}
	
?>
		<div id="responseMessage"><?php echo $message; ?></div>
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
	$('#responseMessage').<?php echo $showMessage;?>();
	
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
						 
					// call the tablesorter plugin 
					$("table").tablesorter({
						widgets: ['zebra'],
						headers: { 0:{sorter: false}}
					}); 

				});
			}
	});
});

function cancel() {
	window.location = "/";
}

function deleteUser() {
	var dialog = '<div id="dialog-confirm" title="Delete user?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Do you really want to remove <span id="deleteUserName"></span> from Jobdash?</p></div>';
	$('body').append(dialog);
	var uid = $("input[name='delete']:checked").val();
	if (uid != "") {
		$.get('user/delete', {id: uid, getName: 'true'}, function (html) {
				$('#dialog-confirm #deleteUserName').html(html);
		});	
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height:200,
			width:300,
			modal: true,
			buttons: {
				"Delete user": function() {
					window.location = 'user/delete/' + uid;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

	}
}

function next() {
	var uid = $("input[name='delete']:checked").val();
	window.location = 'user/edit/' + uid;
}
</script>

</body>
</html>