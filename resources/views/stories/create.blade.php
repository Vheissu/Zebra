<x-layout title="Submit" section="submit">
    <h1 class="page-title">Submit a story</h1>
    <p class="lede">Share a link, ask a question, or both. Text posts show up under <a href="{{ route('ask') }}">Ask</a>.</p>

    <form method="POST" action="{{ route('stories.store') }}" class="form">
        @csrf
        @include('stories.fields', ['story' => null])
        <div class="actions">
            <button type="submit" class="button">Submit</button>
        </div>
    </form>
</x-layout>
