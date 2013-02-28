{extends file='layout.zebra.tpl'}

{block name=content}
	
	{get_flashdata('error')}
	{if $comments}
		<div id="story-book">
			{foreach $comments AS $comment}
				{assign var="usernames" value=get_username($comment->user_id)}
				<div class="content-row">

					{if logged_in()}
					<div class="content-voting">
						<a href="javascript:void(0);" data-comment-id="{$comment->id}" data-vote-action="up" class="upvote{if comment_upvoted($comment->id)} disabled{/if}">&#9652;</a>
						<span class="content-upvotes">{$story->upvotes}</span>
						<a href="javascript:void(0);" data-comment-id="{$comment->id}" data-vote-action="down" class="downvote{if comment_downvoted($comment->id)} disabled{/if}">&#9662;</a>
					</div>
					{/if}

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