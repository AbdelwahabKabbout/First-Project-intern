@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
@endphp




<!DOCTYPE html>
<html lang="en">

<head>
    <title>Guestbook Categories</title>

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

    <h1>Guestbook Categories</h1>
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <form action="{{ route('categories.index') }}" method="GET">
        <a href="{{ route('guestbook.index') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}"><---Back to Guestbook</a>

                <a href="{{ route('categories.create') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Add New Category</a>

                @if($currentTheme === 'dark')
                <input type="hidden" name="theme" value="dark">
                @endif

                <input type="text" name="name" placeholder="Search for Name" value="{{ request('name') }}">
                <button type="submit">Search</button>

                @if(request('name'))
                <a href="{{ route('categories.index') }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Clear Search</a>
                @endif
    </form>

    @if($categories->count() > 0)
    @foreach ($categories as $category)
    <div class="entry">
        <h3>Category: {{ $category->name }}</h3>
        <p>Description: {{$category->description }}</h3>
        <p>Created at: {{ $category->created_at }}</p>
        <div class="actions">
            <a href="{{ route('categories.edit', $category->id) }}{{ $currentTheme === 'dark' ? '?theme=dark' : '' }}">Edit</a>
            <a href="{{ route('categories.deleteAlert', $category->id) }}">Delete</a>
        </div>

    </div>
    @endforeach
    @else
    <p>No categories found.</p>
    @endif

</body>

</html>