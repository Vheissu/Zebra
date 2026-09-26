<h1 class="page-title">Moderation</h1>
<ul class="subnav">
    <li><a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Overview</a></li>
    <li><a href="{{ route('admin.stories') }}" @if(request()->routeIs('admin.stories')) aria-current="page" @endif>Stories</a></li>
    <li><a href="{{ route('admin.comments') }}" @if(request()->routeIs('admin.comments')) aria-current="page" @endif>Comments</a></li>
    <li><a href="{{ route('admin.users') }}" @if(request()->routeIs('admin.users')) aria-current="page" @endif>Members</a></li>
</ul>
