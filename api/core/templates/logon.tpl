{include file='inc/header.tpl'}
<style type="text/css">
	{literal}
	body {
		background-color: #787878;
		font-family: "Trebuchet MS", Helvetica, Jamrul, sans-serif;
		color: #ccc;
	}
	h2 {
		font-weight: normal;
		font-size: 18px;
		margin-left: 5px;
	}
	#login_form {
		background-color: #111111;
		height: 306px;
		margin: 0 auto;
		margin-top:80px;
		overflow: hidden;
		padding: 10px;
		width: 550px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		-webkit-box-shadow: 0px 2px 6px #000000;
		-moz-box-shadow: 0px 2px 6px #000000;
		box-shadow: 0px 2px 6px #000000;
		
	}
	#footer {
		width: 550px;
		margin: 0 auto;
		font-size: 11px;
		text-align: right;
		margin-top: 4px;
	}
	.input {
		background: none repeat scroll 0 0 #232020;
		border: 1px solid #666666;
		border-radius: 5px 5px 5px 5px;
		color: #333;
		padding: 3px;
		width: 267px;
		font-size: 16px;
		margin: 10px 0px 20px 5px;
		height: 30px;
	}
	.input:focus {
		color: #fff;
		webkit-box-shadow: 0 0 4px #FFFFFF;
		-moz-box-shadow: 0 0 4px #FFFFFF;
		box-shadow: 0 0 4px #FFFFFF;
	}
	.button {
		background: none repeat scroll 0 0 #232020;
		border: 1px solid #666666;
		border-radius: 5px 5px 5px 5px;
		color: #CCCCCC;
		font-size: 16px;
		height: 30px;
		margin-left: 5px;
		margin-bottom: 10px;
		width: 544px;
	}
	#footer a {
		color: #fff;
		text-decoration: none;
	}
	{/literal}
</style>
<div id="login_form">
	<img alt="appstack.io logo" src="http://static.appstack.io/img/logo_no2.png">
	
	{if isset($smarty.request.goto)}
	<h2>oAuth login</h2>
	<form method="post">
		
		<input type="hidden" name="goto" value="{$smarty.request.goto}" />
		<input id="username" onfocus="if(this.value == 'Username') { this.value = ''; }" type="text" name="username" id="username"  value="Username" class="input"/>
		<input onfocus="if(this.value == 'Password') { this.value = ''; }" type="password" name="password" id="password" value="Password" class="input"/>
		<input type="submit" value="Login" class="button" />
		<input onclick="forgotPassword();" type="button" value="Forgot password" class="button" />
	</form>
	{else}
	<h2>Redirect</h2>
	<script type="text/javascript">
		window.location.replace('http://dev.appstack.io');
	</script>
	{/if}
</div>
<div id="footer">
	<a href="http://appstack.io/" title="appstack.io" class="ftr_link">&copy; appstack.io 2012</a> | <a href="http://appstack.io/privacy/" title="appstack.io/privacy" class="ftr_link">Privacy Policy</a> | <a href="http://blog.appstack.io/" title="blog.appstack.io" class="ftr_link">Blog</a>
</div>
<script type="text/javascript">
function forgotPassword(){
	var usr = document.getElementById('username').value;
	window.location.replace('http://dev.appstack.io/account/nopw/?username='+usr+'&return=1');
	return false;
}
window.scrollTo(0, 1);
</script>
{include file='inc/footer.tpl'} 