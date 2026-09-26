@props(['time'])
<time datetime="{{ $time->toIso8601String() }}" title="{{ $time->toDayDateTimeString() }}">{{ $time->diffInSeconds(now()) < 60 ? 'just now' : $time->diffForHumans() }}</time>
