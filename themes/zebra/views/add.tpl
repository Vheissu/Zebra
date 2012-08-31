{extends file='layout.zebra.tpl'}

{block name=content}
	<form id="submission-form" action="" method="POST">
		<div class="form-row">
			<label>Title:</label> <input type="text" name="title" id="title" placeholder="Title">
		</div>
		<div class="form-row">
			<label>Link:</label> <input type="text" name="link" id="link" placeholder="http://www.somewebsite.com">
		</div>
		<div class="form-row">
			<label>Text:</label> <textarea name="text" id="text" placeholder="http://www.somewebsite.com"></textarea>
		</div>
	</form>
{/block}