<?php
	
	if (isset($_POST['loginFld'])) {
		include 'functions.php';	
		$email = sanitizeString($_POST['email']);
		$password = sanitizeString($_POST['password']);
		
		$checkUser = login($email, $password);
		if ($checkUser == false) {
			return "false";
		} else {
			$_SESSION['user'] = $checkUser;
			return $checkUser;
		}		
	} else {
?>

    <div id="login">
      <form id="frm_login" name="frm_login" onsubmit="return false;">
        <input type="text" name="email" id="email" />
        <input type="password" name="password" id="password" />
        <button id="btn_login" class="submit small">Log In</button><input type="hidden" name="loginFld" value="login" />
      </form>
      <div id="error">
      </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
	$('#frm_login').submit(function() {
		var login = submitLogin($('#email').val(), $('#password').val());
		if (login == "false") {
			Alert('You have not logged in successfully');
		} else {
			alert(login);
		}
	});
});
</script>

<?php
	}
?>