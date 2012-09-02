{extends file='layout.zebra.tpl'}

{block name=content}
	<form id="submission-form" action="story/submit" method="POST">
		<div class="form-row">
			<input type="text" name="title" id="title" placeholder="Title">
		</div>
		<div class="form-row">
			<input type="text" name="link" id="link" placeholder="Website URL">
		</div>
		<div class="form-row">
			<textarea name="text" id="text" placeholder="Something smart, something witty." rows="6" cols="50"></textarea>
		</div>
		<div class="form-row button-row">
			<input type="submit" name="submit" value="Submit">
		</div>
	</form>
{/block}