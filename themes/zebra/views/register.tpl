{extends file='layout.zebra.tpl'}

{block name=content}
	<form id="login-form" action="login" method="POST">
		<div class="form-row">
			<input type="text" name="username" id="username" placeholder="Username max characters 20">
		</div>
		<div class="form-row">
			<input type="email" name="email" id="email" placeholder="Make sure it's valid in-case you forget your password">
		</div>
		<div class="form-row">
			<input type="password" name="password" id="password" placeholder="Password">
		</div>
		<div class="form-row button-row">
			<input type="submit" name="submit" value="Register">
		</div>
	</form>
{/block}