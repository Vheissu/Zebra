{extends file='layout.zebra.tpl'}

{block name=content}
    {validation_errors()}
    <form action="comment/edit/{$comment->id}" method="POST">
        <textarea name="comment" id="comment" rows="6" cols="50">{$comment->comment}</textarea><br><br>
        <input type="submit" value="Comment"><br>
    </form>
{/block}