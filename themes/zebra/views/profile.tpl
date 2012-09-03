{extends file='layout.zebra.tpl'}

{block name=content}
	{get_flashdata('error')}
	{if $user}
	<div id="profile">
		<div class="profile-row">
			<p><strong>Info</strong></p>
			<p><span>Username:</span> {$user.username}</p>
			<p><span>First name:</span> {$user.meta.first_name}</p>
			<p><span>Last name:</span> {$user.meta.last_name}</p>
			<p><strong>Submissions</strong></p>
			<p>Number of submissions: {$user.meta.submissions}</p>
		</div>
	</div>
	{/if}
{/block}