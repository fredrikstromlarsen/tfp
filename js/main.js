function showPassword(id) {
	if (document.getElementById(id).type == 'password') document.getElementById(id).type = 'text';
	else document.getElementById(id).type = 'password';
}
