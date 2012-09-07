{extends file='layout.zebra.tpl'}

{block name=content}
	
	{get_flashdata('error')}
	{validation_errors()}
	{if $story}
		{assign var="usernames" value=get_username($story->user_id)}
		<div id="story-book">
			<div class="story-row">
				<div class="story-voting">
					<a href="javascript:void(0);" data-story-id="{$story->id}" data-vote-action="up" class="upvote{if story_upvoted($story->id)} disabled{/if}">&#9652;</a>
					<span class="story-upvotes">{$story->upvotes}</span>
					<a href="javascript:void(0);" data-story-id="{$story->id}" data-vote-action="down" class="downvote{if story_downvoted($story->id)} disabled{/if}">&#9662;</a>
				</div>
				<div class="story-meat">
					{if $story->external_link}
						<a class="story-title" href="{$story->external_link}" target="_blank">{$story->title}</a>
						{* See: application/helpers/zebra_helper.php to see where this function is defined *} 
						<span class="story-domain">({get_domain($story->external_link)})</span>
					{else}
						<a class="story-title" href="story/{$story->id}/{$story->slug}">{$story->title}</a>
					{/if}
					<div class="story-meta">
						<p>by <a href="user/{$usernames.username}" class="username">{$usernames.nice_username}</a> {timespan($story->created, time(), 1)} ago | <a href="story/{$story->id}/{$story->slug}#comments">{$story->comment_count} comments</a></p>
					</div>
				</div>

				{if !$story->external_link AND $story->description}
				<div id="story-description">
					{$story->description}
				</div>
				{/if}

				
				<div id="comment-form">
					{if logged_in()}
					<form action="story/{$story->id}/comment" method="POST">
						<textarea name="comment" id="comment" rows="6" cols="50"></textarea><br><br>
						<input type="submit" value="Comment"><br>
						<input type="hidden" name="in_reply_to" id="in_reply_to" value="0">
					</form>
					{else}
					<p class="not-logged-in">You must be logged in to leave a comment.</p>
					{/if}
				</div>
				{if $story->comment_count >= 1}
					<div id="comments">
						{* See: application/modules/comment/helpers/comment_helper.php to see where this function is defined *}
						{display_comments($story->id)}
					</div>
				{/if}
			</div>
		</div>
	{else}
		<div id="empty-book">
			<p>Sorry, there are no stories to display. Either something broke or nobody has submitted anything yet.</p>
		</div>
	{/if}
{/block}