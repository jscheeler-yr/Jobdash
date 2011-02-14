// JavaScript Document

function submitLogin(username, password) {
	$.ajax({
		url: "scripts/login.php",
		data: ({email: username, password: password}),
		type: "POST",
		dataType: "text",
		success: function (result) {
			if (result) {
				return true;
			} else {
				return false;
			}
		}
	});
}