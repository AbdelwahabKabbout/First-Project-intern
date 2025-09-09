@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Add Category - Guestbook</title>
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
    <h1>Add New Category</h1>

    <a href="{{ route('categories.index') }}">&larr; Back to Categories</a>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}">
        <label for="description">Description</label>
        <input type="text" id="description" name="description" value="{{ old('description') }}">


        <button type="submit">Add Category</button>
    </form>
</body>

</html>