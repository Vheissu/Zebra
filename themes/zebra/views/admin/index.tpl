{extends file='layout.admin.tpl'}

{block name=content}
	{get_flashdata('error')}
	<h2>Administration Dashboard</h2>

	<section class="admin-block">
		<p><strong>Total submissions:</strong> {$analytics->total_submissions}</p>
		<p><strong>Last submission:</strong> {$analytics->last_submission->title} - added {timespan($analytics->last_submission->created, time(), 1)} ago</p>
	</section>
{/block}