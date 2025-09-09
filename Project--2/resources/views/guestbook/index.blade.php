@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
$categorySelected=[];
@endphp


<!DOCTYPE html>
<html>

<head>
    <title>Guestbook</title>

    @php
    $currentTheme = $_SESSION['theme'] ?? 'light';
    @endphp

    @if($currentTheme === 'dark')
    @vite('resources/css/Dark.css')
    @else
    @vite('resources/css/Light.css')
    @endif


</head>

<body>

    @include('components.themeToggle')

    <h1>Guestbook</h1>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <form action="{{ route('guestbook.index') }}" method="GET">
        <a href="{{ route('guestbook.create') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Add New Message</a>
        <a href="{{ route('categories.index') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Manage Categories</a>
        <a href="{{ route('guestbook.stats') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">View Stats</a>
        <a href="{{ route('guestbook.manageUser') }}{{ $currentTheme === 'dark' ? '?theme=dark' : ''  }}">Manage Users</a>

        @if($currentTheme === 'dark')
        <input type="hidden" name="theme" value="dark">
        @endif

        <input type="text" name="name" placeholder="Search for Name" value="{{ request('name') }}">

        <div class="dateTime">
            <label for="startdate">Choose starting date</label>
            <input type="date" id="startdate" name="startdate" value="{{ request('startdate') }}">

            <label for="enddate">Choose ending date</label>
            <input type="date" id="enddate" name="enddate" value="{{ request('enddate') }}">
        </div>

        <label>
            <input type="checkbox" name="picture" value="1" {{ request('picture') ? 'checked' : '' }}>
            Only show entries with a picture
        </label>

        <label>

            <select name="due">
                <option value="createdAt" {{ request('due')=='createdAt' ? 'selected':'' }}>
                    Sort by Creation time
                </option>
                <option value="accending" {{ request('due') == 'accending' ? 'selected' : '' }}>
                    Sort Due Date by ascending
                </option>
                <option value="deccending" {{ request('due') == 'deccending' ? 'selected' : '' }}>
                    Sort Due Date by descending
                </option>
            </select>
        </label>


        <div class="category-container">
            @foreach ($categories as $category)
            <label class="category-label-Search">
                <input type="radio"
                    name="guest_category_id"
                    value="{{ $category->id }}"
                    {{ old('guest_category_id', request()->input('guest_category_id', $categorySelected ?? null)) == $category->id ? 'checked' : '' }}>
                {{ $category->name }}
            </label>
            @endforeach

            <label class="category-label-Search select-all">
                <input type="radio"
                    name="guest_category_id"
                    value=""
                    {{ old('guest_category_id', request()->input('guest_category_id', null)) === null ? 'checked' : '' }}>
                Select All
            </label>
        </div>

        <button type="submit">Search</button>

        @if(request('name') || request('startdate') || request('enddate')|| request('picture'))
        <a href="{{ route('guestbook.index') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Clear Search</a>
        @endif
    </form>


    @if($entries->count() > 0)
    @foreach ($entries as $entry)
    <div class="entry">
        <h3>Name: {{ $entry->name }}</h3>
        @if($entry->email)
        <p><em>Email: {{ $entry->email }}</em></p>
        @endif
        <p>Message: {{ $entry->message }}</p>

        <p>Tag:
            @if($entry->tag)
            {{ $entry->tag }}
            @else
            No Tags
            @endif
        </p>

        @if($entry->image)
        <img src="data:{{ $entry->image_type }};base64,{{ base64_encode($entry->image) }}" alt="Image">
        @endif


        <div class="show-rate-container">
            @switch($entry->rate)
            @case('happy')
            <img src="/GbookImages/001-happy-face.png" alt="Happy">
            @break
            @case('smile')
            <img src="/GbookImages/004-smile.png" alt="Smile">
            @break
            @case('neutral')
            <img src="/GbookImages/003-confused.png" alt="Neutral">
            @break
            @case('sad')
            <img src="/GbookImages/002-sad.png" alt="Sad">
            @break
            @case('angry')
            <img src="/GbookImages/005-angry.png" alt="Angry">
            @break
            @default
            <span>No Rating</span>
            @endswitch
        </div>

        <p>Created at: {{ $entry->created_at }}</p>

        @php
        $today = date('Y-m-d');
        @endphp

        @if ($entry->due_date === $today)
        <p style="color: chartreuse;">Due Date: {{ $entry->due_date }}</p>
        @elseif ($entry->due_date < $today)
            <p style="color: red;">Due Date: {{ $entry->due_date }}</p>
            @else
            <p>Due Date: {{ $entry->due_date }}</p>
            @endif

            <p class=" category-info">
                <span class="category-label">Category:</span>
                @if($entry->guest_category_id)

                @if($entry->category->trashed())
                <span class="category-tag deleted">Deleted- {{ optional($entry->category)->name ?? 'Unknown' }}</span>
                @else
                <span class="category-tag">{{ optional($entry->category)->name ?? 'Unknown' }}</span>
                @endif

                @else
                <span class="category-tag none">None</span>
                @endif
            </p>
            <div class="actions">
                <a href="{{ route('guestbook.edit', $entry->id) }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Edit</a>

                <form action="{{ route('guestbook.delete', $entry->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    @if($currentTheme === 'dark')
                    <input type="hidden" name="theme" value="dark">
                    @endif
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this entry?')">Delete</button>
                </form>
            </div>
    </div>
    @endforeach

    @if($entries->lastPage() > 1)
    <div class="pagination">
        @if ($entries->onFirstPage())
        <span class="disabled">« Previous</span>
        @else
        <a href="{{ $entries->appends(array_merge(request()->query(), ['theme' => $currentTheme]))->previousPageUrl() }}">« Previous</a>
        @endif


        @php
        $start = max(1, $entries->currentPage() - 2);
        $end = min($entries->lastPage(), $entries->currentPage() + 2);
        @endphp

        @if($start > 1)
        <a href="{{ $entries->appends(array_merge(request()->query(), ['theme' => $currentTheme]))->url(1) }}">1</a>
        @if($start > 2)
        <span class="disabled">...</span>
        @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            @if ($i==$entries->currentPage())
            <span class="current">{{ $i }}</span>
            @else
            <a href="{{ $entries->appends(array_merge(request()->query(), ['theme' => $currentTheme]))->url($i) }}">{{ $i }}</a>
            @endif
            @endfor

            @if($end < $entries->lastPage())
                @if($end < $entries->lastPage() - 1)
                    <span class="disabled">...</span>
                    @endif
                    <a href="{{ $entries->appends(array_merge(request()->query(), ['theme' => $currentTheme]))->url($entries->lastPage()) }}">{{ $entries->lastPage() }}</a>
                    @endif

                    @if ($entries->hasMorePages())
                    <a href="{{ $entries->appends(array_merge(request()->query(), ['theme' => $currentTheme]))->nextPageUrl() }}">Next »</a>
                    @else
                    <span class="disabled">Next »</span>
                    @endif
    </div>
    @endif
    @else
    @if(request('name'))
    <p>No messages found matching "{{ request('name') }}". <a href="{{ route('guestbook.index') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Show all messages</a></p>
    @else
    <p>No messages yet. <a href="{{ route('guestbook.create') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Be the first to post!</a></p>
    @endif
    @endif
</body>

</html>