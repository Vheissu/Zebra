{extends file='layout.zebra.tpl'}

{block name=content}
	{get_flashdata('error')}

	{if $user}
	<div id="profile">
		<div class="profile-row">
			<p><strong>Info</strong></p>
			<p><span>User:</span> {$user.username}</p>
			<p><span>Created:</span> {timespan($user.register_date, now(), 1)} ago</p>
			<p><span>Karma:</span> {$user.karma}</p>
			<p><span>Average Karma:</span> {$user.average_karma}</p>
			<p><strong>Submissions</strong></p>
			<p><span>Number of submissions:</span> {$user.meta->submissions}</p>
			<p><span>Average submissions per day:</span> {$user.average_submissions}</p>
			<p><a href="user/{$user.username|strtolower}/stories">View all submissions</a></p>
		</div>
	</div>
	{/if}
{/block}