{extends file='layout.zebra.tpl'}

{block name=content}
	
	{get_flashdata('error')}
	{validation_errors()}
	{if $comment}
		{assign var="username" value=get_username($comment->user_id)}
		<div id="story-book">
			<div class="story-row">
				<div class="story-voting">
					<a href="javascript:void(0);" data-story-id="{$comment->id}" data-vote-action="up" class="upvote">&#9652;</a>
					<span class="story-upvotes">{$comment->upvotes}</span>
					<a href="javascript:void(0);" data-story-id="{$comment->id}" data-vote-action="down" class="downvote">&#9662;</a>
				</div>
				<div class="story-meat">
					{$comment->comment}
				</div>
			</div>
		</div>
	{else}
		<div id="empty-book">
			<p>{lang('no_stories')}</p>
		</div>
	{/if}
{/block}