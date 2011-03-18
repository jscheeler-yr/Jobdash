<?php
	if (isset($_GET['logout'])) {
			session_start();
			$_SESSION['user'] = NULL;
			$_SESSION['regionID'] = NULL;
			header('Location: /Jobdash');
	}
		
	if (isset($_POST['loginFld'])) {
		include 'functions.php';	
		session_start();
		$email = sanitizeString($_POST['email']);
		$password = sanitizeString($_POST['password']);
		
		$checkUser = login($email, $password);
		if (!$checkUser) {
			echo "false";
		} else {
			$_SESSION['user'] = $checkUser;
			$regionID = queryArray("SELECT regionID FROM users WHERE id='$checkUser'");
			$rid = $regionID['regionID'];
			$regionSQL = queryArray("SELECT name FROM region WHERE id='$rid'");
			$region = strtolower(str_replace(" ", "", $regionSQL['name']));
			echo $region;
		}		
	} else {
?>

    <div id="login" class="headerTopRight textRight">
      <form id="frm_login" name="frm_login" onsubmit="return false;">
        <button id="btn_login" class="submit small" tabindex="3">Log In</button><input type="hidden" name="loginFld" value="login" />
        <input type="password" name="password" id="password" tabindex="2" />
        <input type="text" name="email" id="email" tabindex="1" />
      </form>
      <div id="error" class="font10">
      </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
	$('#frm_login').submit(function() {
		$.ajax({
			url: "scripts/login",
			data: ({loginFld: 'login', email: $('#email').val(), password: $('#password').val()}),
			type: "POST",
			dataType: "text",
			success: function(result) {
				if (result == "false") {
					$('#error').html('Either your username or password were incorrect.');
				} else {
					window.location = '/Jobdash/'+result;
				}
			}
		});
	});
});
</script>

<?php
	}
?>