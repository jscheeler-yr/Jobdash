// JavaScript Document

function submitLogin(username, password) {
	$.ajax({
		url: "includes/login.php",
		data: ({loginFld: 'login', email: username, password: password}),
		type: "POST",
		dataType: "text",
		success: function (result) {
			return result;
		}
	});
}