@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
@endphp



<!DOCTYPE html>
<html lang="en">

<head>
    <title>Stats</title>
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
    <h1>Guestbook Statistics</h1>

    <a href="{{ route('guestbook.index') }}">&larr; Back to Guestbook</a>

    <h2>Total Entries: {{ $entries->count() }}</h2>

    <h3>Entries by Category:</h3>
    <ul>
        @foreach($entries->groupBy('category.name') as $category => $groupedEntries)
        <li>{{ $category }}: {{ $groupedEntries->count() }}</li>
        @endforeach
    </ul>

    <h3>Entries by Rating:</h3>
    <ul>
        @foreach($entries->groupBy('rate') as $rating => $groupedEntries)
        <li>{{ ucfirst($rating) }}: {{ $groupedEntries->count() }}</li>
        @endforeach
    </ul>


    <h1>Gbook entries</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Category</th>
                <th>Rating</th>
                <th>Image</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td>{{ $entry->name }}</td>
                <td>{{ $entry->email }}</td>
                <td>{{ $entry->message }}</td>
                <td>{{ $entry->category ? $entry->category->name : 'Uncategorized' }}</td>
                <td>
                    @if($entry->rate === 'happy')
                    <img src="/GbookImages/001-happy-face.png" alt="Happy">
                    @elseif($entry->rate === 'smile')
                    <img src="/GbookImages/004-smile.png" alt="Smile">
                    @elseif($entry->rate === 'neutral')
                    <img src="/GbookImages/003-confused.png" alt="Neutral">
                    @elseif($entry->rate === 'sad')
                    <img src="/GbookImages/002-sad.png" alt="Sad">
                    @elseif($entry->rate === 'angry')
                    <img src="/GbookImages/005-angry.png" alt="Angry">
                    @else
                    <span>No Rating</span>
                    @endif
                </td>
                <td>
                    @if($entry->image)
                    <img src="data:{{ $entry->image_type }};base64,{{ base64_encode($entry->image) }}"
                        alt="Entry Image"
                        style="max-width: 100px; max-height: 100px;">
                    @else
                    No Image
                    @endif
                </td>
                <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>



</body>

</html>