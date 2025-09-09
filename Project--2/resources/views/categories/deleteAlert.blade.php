<?php
$option = session('option');
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
?>


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
    <h1>Alert: This Category is already in use</h1>
    <a href="{{ route('categories.index') }}">Cancel Delete</a>
    <form action="{{ route('categories.handleOption', $category->id) }}" method="POST">

        @csrf

        <button type="submit" name="option" value="ChangeCategory">Change Category</button>
        <button type="submit" name="option" value="DeleteAnyway">Delete Anyway</button>
    </form>

</body>

</html>