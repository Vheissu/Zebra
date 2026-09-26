<x-layout title="Edit story">
    <h1 class="page-title">Edit story</h1>

    <form method="POST" action="{{ route('stories.update', $story) }}" class="form">
        @csrf @method('PUT')
        @include('stories.fields')
        <div class="actions">
            <button type="submit" class="button">Save</button>
            <a href="{{ $story->permalink() }}">Cancel</a>
        </div>
    </form>
</x-layout>
