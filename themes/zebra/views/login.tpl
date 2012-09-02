{extends file='layout.zebra.tpl'}

{block name=content}
	<form id="login-form" action="login" method="POST">
		<div class="form-row">
			<input type="text" name="username" id="username" placeholder="Username">
		</div>
		<div class="form-row">
			<input type="password" name="password" id="password" placeholder="Password">
		</div>
		<div class="form-row">
			<label>Remember me? <input type="checkbox" name="remember_me" id="remember_me" value="yes"></label>
		</div>
		<div class="form-row button-row">
			<input type="submit" name="submit" value="Login">
		</div>
	</form>
{/block}