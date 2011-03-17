<?php
	require_once '../header.php';
	
	if ($_GET['action'] == 'add') {
		$action = 'add';
		$pageTitle = "Add User";
		$frmAction ='add/submit';
		$btnAction = 'Add User';
		
	} else if ($_GET['action'] == 'edit') {
		$uid = $_GET['uid'];
		$editUser = mysql_fetch_array(queryMySQL("SELECT * FROM " . USERTABLE . " WHERE id='$uid'"));
		$action = 'edit';
		$pageTitle = "Edit User: " . $editUser['firstname'] . " " . $editUser['lastname'];
		$frmAction ='submit?uid='.$uid;
		$btnAction = 'Save';
	}
	
	//Get the departments
	$departments = queryMySQL("SELECT * FROM departments");
	$departmentsHTML = "";
	while($row=mysql_fetch_array($departments)) {
		$departmentsHTML .= '<option value="'.$row['id'].'">'.$row['name'].'</option>';
	}
	
	//Get the titles
	$titles = queryMySQL("SELECT * FROM user_titles");
	$titlesHTML = "";
	while($row=mysql_fetch_array($titles)) {
		$titlesHTML .= '<option value="'.$row['id'].'">'.$row['abbr'] . ' - ' . $row['full'].'</option>';
	}
	
	//Get the regions
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

	//Get the permissions levels
	$permissions = queryMySQL("SELECT * FROM user_permissions");
	$permissionsHTML = "";
	while($row=mysql_fetch_array($permissions)) {
		//If the user only has regional accession, permissions level 2 only allow them to choose their region
		if (($row['id'] < $user_perm)) {
			$disabled = 'disabled="disabled"';
		} else {
			$disabled = "";
		}
		$permissionsHTML .= '<option value="'.$row['id'].'" '. $disabled .'>'.$row['name'].'</option>';
	}

?>
<h2><?php echo $pageTitle;?></h2>
	<form id="frmUserInfo" method="post">
  	<p><label for="firstname">First Name: </label>
    <input type="input" id="firstname" name="firstname" /></p>
  	<p><label for="lastname">Last Name: </label>
    <input type="input" id="lastname" name="lastname" /></p>
  	<p><label for="department">Department: </label>
    <select id="department" name="department">
    	<option>--- Select Deparment ---</option>
    	<?php echo $departmentsHTML; ?>
    </select></p>
  	<p><label for="selectTitle">Title: </label>
    <select id="selectTitle" name="selectTitle">
    	<option>--- Select Title ---</option>
    	<?php echo $titlesHTML; ?>
    </select></p>
  	<p><label for="phone">Phone: </label>
    <input type="input" id="phone" name="phone" /></p>
<?php
	if ($action == 'add') {
?>
  	<p><label for="email">E-mail: </label>
    <input type="input" id="email" name="email" /></p>
  	<p><label for="email2">Confirm E-mail: </label>
    <input type="input" id="email2" name="email2" /></p>
<?php
	} 
	
	if ($action == 'edit'){
?>
  	<p><label for="currentPassword">Current Password: </label>
    <input type="input" id="currentPassword" name="currentPassword" /><span class="error"></span></p>
  	<p><label for="password">Password: </label>
    <input type="input" id="password" name="password" /><span class="error"></span></p>
  	<p><label for="password2">Confirm Password: </label>
    <input type="input" id="password2" name="password2" /></p>
<?php
	}
	
	if ($user_perm != 3) {
?>
  	<p><label for="region">Region: </label>
    <select id="region" name="region">
    	<option>--- Select Region ---</option>
	    <?php echo $regionsHTML; ?>
    </select></p>
  	<p><label for="permissions">Permissions Level: </label>
    <select id="permissions" name="permissions">
    	<option>--- Select Permissions ---</option>
    <?php echo $permissionsHTML; ?>
    </select></p>
<?php 
	}
?>
  </form>
 <div id="buttons">
  <a id="btnCancel" class="submit orange medium" onclick="cancel();">Cancel</a>
  <a id="btnAddUser" class="submit green medium" onclick="frmSubmit();"><?php echo $btnAction; ?></a>
</div>

<?php
	require_once '../footer.php';
?>
<script type="text/javascript">
$('input[name="phone"]').setMask('(999) 999-9999');
$(document).ready(function() {  
	//Send information to the add script (includes/addUser.php);
	$('#frmUserInfo').attr('action', '<?php echo $frmAction; ?>');
	

<?php
	if ($action == 'edit') {
?>
	var check = false;
	if (!check) {
		$('#currentPassword').blur(function() {
			if ($(this).val() != ""){
				$.get('checkpass', {uid: '<?php echo $uid; ?>', pass:$('#currentPassword').val()}, function (response) {
						if (response != "true") {
							$('#currentPassword').parent().children('.error').html('Wrong password');
							check = true;
						}
				});	
			}
		});
	}
	
	$('#currentPassword').focus(function() {
		check = false;
	});

		$('#firstname').val('<?php echo $editUser['firstname'];?>');	
		$('#lastname').val('<?php echo $editUser['lastname'];?>');	
		$('#department option[value="<?php echo $editUser['departmentID'];?>"]').attr('selected', 'selected');	
		$('#selectTitle option[value="<?php echo $editUser['titleID'];?>"]').attr('selected', 'selected');	
		$('#phone').val('<?php echo $editUser['phone'];?>');
<?php 
			if ($user_perm != 3) {
?>
			
		$('#region option[value="<?php echo $editUser['regionID'];?>"]').attr('selected', 'selected');			
		$('#permissions option[value="<?php echo $editUser['permissionsID'];?>"]').attr('selected', 'selected');		
<?php
			}
	}
?>

});

function frmSubmit() {
<?php if ($action=='edit') {?>
	if ($('#currentPassword').val() != "") {
		if ($('#password').val() != $('#password2').val()) {
			$('#password').parent().children('.error').html('Passwords do not match');
			return false;
		} else {
			return false;
		}
	} else {
		$('#frmUserInfo').submit();
	}
<?php } else { ?>
		$('#frmUserInfo').submit();
<?php } ?>
}

function cancel() {
	window.location = "<?php echo DOC_ROOT;?>admin/";
}
</script>

</body>
</html>