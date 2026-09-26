<form method="GET" class="filters" role="search">
    <label for="q" class="visually-hidden">Search</label>
    <input type="search" id="q" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}">
    @foreach($options as $value => $label)
        <label class="checkbox"><input type="radio" name="show" value="{{ $value }}" @checked(request('show', '') === $value)> {{ $label }}</label>
    @endforeach
    <button type="submit" class="button button--quiet">Filter</button>
</form>
