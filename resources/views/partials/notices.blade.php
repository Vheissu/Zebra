@if(session('status'))
    <p class="notice" role="status">{{ session('status') }}</p>
@endif

@foreach(['vote', 'reason', 'admin'] as $key)
    @error($key)
        <p class="notice notice--error" role="alert">{{ $message }}</p>
    @enderror
@endforeach
