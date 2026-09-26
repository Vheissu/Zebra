<div class="field">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" maxlength="200" required autofocus value="{{ old('title', $story?->title ?? ($prefill['title'] ?? '')) }}">
    @error('title')<p class="error">{{ $message }}</p>@enderror
</div>
<div class="field">
    <label for="url">Link</label>
    <input type="url" id="url" name="url" maxlength="2048" placeholder="https://" value="{{ old('url', $story?->url ?? ($prefill['url'] ?? '')) }}">
    @error('url')<p class="error">{{ $message }}</p>@enderror
</div>
<div class="field">
    <label for="text">Text</label>
    <textarea id="text" name="text" rows="7" maxlength="10000">{{ old('text', $story?->text) }}</textarea>
    <p class="hint">Optional with a link. Without one, this becomes an Ask post.</p>
    @error('text')<p class="error">{{ $message }}</p>@enderror
</div>
@include('partials.formatting-help')
