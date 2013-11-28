function loginForm() {
	var sTemplate = '<input type="text" name="usr" value="Username" class="system_login tia_green"/>';
		sTemplate += '<input type="password" name="pwd" value="password" class="system_login tia_green"/>';
		sRequestSender = 'user/login';
		openDialog('message', 'Session expired!', sTemplate, 'Login', 'Abort', true);
}