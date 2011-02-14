    <div id="login">
      <form method="post" id="frm_login" name="frm_login">
        <input type="text" name="email" id="email" />
        <input type="password" name="password" id="password" />
        <button id="btn_login" class="submit small">Log In</button><input type="hidden" name="login-fld" value="login" />
      </form>
      <div id="error">
      </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
	$('#frm_login').submit(function() {
		var login = submitLogin($('#email').val(), $('#password').vall());
		if (login) {
			window.location = "index.php";
		} else {
			
		}
		return false;
	});
});
</script>
