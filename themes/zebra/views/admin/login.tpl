{extends file='layout.admin.tpl'}

{block name=content}
	{get_flashdata('error')}
	{validation_errors()}
	<h2>Administration</h2>
	<form id="login-form" action="admin/login" method="POST">
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