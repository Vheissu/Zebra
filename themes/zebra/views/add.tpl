{extends file='layout.zebra.tpl'}

{block name=content}
	{validation_errors()}
	{if logged_in()}
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
		<div class="form-row">
			<p>If you enter a URL the text field is ignored. If you want to ask a question or make<br>discussion don't provide a URL.</p>
		</div>
		<div class="form-row button-row">
			<input type="submit" name="submit" value="Submit">
		</div>
	</form>
	{else}
		<div id="error-div">You must be logged in submit stuff.</div>
	{/if}
{/block}