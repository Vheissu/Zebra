<x-layout title="Edit comment">
    <h1 class="page-title">Edit comment</h1>
    <p class="lede">On <a href="{{ $comment->story->permalink() }}">{{ $comment->story->title }}</a></p>

    <form method="POST" action="{{ route('comments.update', $comment) }}" class="form">
        @csrf @method('PUT')
        <div class="field">
            <label for="body">Comment</label>
            <textarea id="body" name="body" rows="8" required maxlength="10000">{{ old('body', $comment->body) }}</textarea>
            @error('body')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="actions">
            <button type="submit" class="button">Save</button>
            <a href="{{ $comment->story->permalink() }}#c{{ $comment->id }}">Cancel</a>
            @include('partials.formatting-help')
        </div>
    </form>
</x-layout>
