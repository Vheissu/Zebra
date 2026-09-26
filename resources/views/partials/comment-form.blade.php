@php($fieldId = isset($parent) ? 'reply-body' : 'comment-body')
<form method="POST" action="{{ $action }}" class="form {{ isset($inline) ? 'form--inline' : '' }}">
    @csrf
    @isset($parent)
        <input type="hidden" name="parent_id" value="{{ $parent ?: '' }}">
    @endisset
    <div class="field">
        <label for="{{ $fieldId }}" @isset($inline) class="visually-hidden" @endisset>{{ $label }}</label>
        <textarea id="{{ $fieldId }}" name="body" rows="5" required maxlength="10000">{{ isset($inline) ? '' : old('body') }}</textarea>
        @unless(isset($inline))
            @error('body')<p class="error">{{ $message }}</p>@enderror
        @endunless
    </div>
    <div class="actions">
        <button type="submit" class="button">{{ isset($parent) ? 'Reply' : 'Comment' }}</button>
        @isset($inline)
            <button type="button" class="button button--quiet" data-cancel-reply>Cancel</button>
        @endisset
        @include('partials.formatting-help')
    </div>
</form>
