@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Edit Category</title>
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
    <h1>Edit Category</h1>

    <a href="{{ route('categories.index') }}">&larr; Back to Categories</a>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}">

        <label for="description">Description:</label>
        <textarea name="description" id="description">{{ old('description', $category->description) }}</textarea>

        <button type="submit">Update Category</button>
    </form>

</body>

</html>