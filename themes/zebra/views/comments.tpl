{extends file='layout.zebra.tpl'}

{block name=content}
	
	{get_flashdata('error')}
	{if $comments}
		<div id="story-book">
			{foreach $comments AS $comment}
				{assign var="usernames" value=get_username($comment->user_id)}
				<div class="content-row">
					<div class="content-meat">
						{$comment->comment}
					</div>
				</div>
			{/foreach}
		</div>
	{else}
		<div id="empty-book">
			<p>Sorry, there are no comments to display.</p>
		</div>
	{/if}
{/block}