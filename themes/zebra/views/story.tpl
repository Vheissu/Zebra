{extends file='layout.zebra.tpl'}

{block name=content}
	
	{get_flashdata('error')}
	{validation_errors()}
	{if $story}
		{assign var="usernames" value=get_username($story->user_id)}
		<div id="story-book">
			<div class="content-row">
				<div class="content-voting">
					<a href="javascript:void(0);" data-story-id="{$story->id}" data-vote-action="up" class="upvote{if story_upvoted($story->id)} disabled{/if}">&#9652;</a>
					<span class="story-upvotes">{$story->upvotes}</span>
					<a href="javascript:void(0);" data-story-id="{$story->id}" data-vote-action="down" class="downvote{if story_downvoted($story->id)} disabled{/if}">&#9662;</a>
				</div>
				<div class="content-meat">
					{if $story->external_link}
						<a class="story-title" href="{$story->external_link}" target="_blank">{$story->title}</a>
						{* See: application/helpers/zebra_helper.php to see where this function is defined *} 
						<span class="content-domain">({get_domain($story->external_link)})</span>
					{else}
						<a class="content-title" href="story/{$story->id}/{$story->slug}">{$story->title}</a>
					{/if}
					<div class="content-meta">
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
					<p class="not-logged-in">{lang('comment_login')}</p>
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
			<p>{lang('no_stories')}</p>
		</div>
	{/if}
{/block}