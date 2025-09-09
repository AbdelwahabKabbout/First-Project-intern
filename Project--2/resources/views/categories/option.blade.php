@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
@endphp



<!DOCTYPE html>
<html lang="en">

<head>
    <title>Document</title>
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
    @if ($option == 'ChangeCategory')
    <h1>Please select a new category for the items in this category</h1>
    <form action="{{ route('categories.UpdateThenDestroy', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="new_category">New Category:</label>
        <select name="new_category" id="new_category" required>
            @foreach($allCategories as $cat)
            @if($cat->id != $category->id)
            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endif
            @endforeach
        </select>
        <button type="submit">Update Category</button>

    </form>

    @elseif ($option == 'DeleteAnyway')
    <h1>Are you sure you want to delete this category?</h1>
    <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Yes, Delete Category</button>
    </form>
    @endif

    <a href="{{ route('categories.index') }}">Cancel Delete</a>
</body>

</html>