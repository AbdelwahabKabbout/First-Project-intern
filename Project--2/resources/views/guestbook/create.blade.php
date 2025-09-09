@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
$categories = App\Models\GuestCategory::whereNull('deleted_at')->orderBy('name')->get();
@endphp


<!DOCTYPE html>
<html>

<head>
    <title>Add Message - Guestbook</title>
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
    <h1>Add New Message</h1>

    <a href="{{ route('guestbook.index') }}">&larr; Back to Guestbook</a>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <form action="{{ route('guestbook.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}">

        <label for="email">Email (optional):</label>
        <input type="text" id="email" name="email" value="{{ old('email') }}">

        <label for="message">Message:</label>
        <textarea id="message" name="message">{{ old('message') }}</textarea>

        <label for="tag">Tag:</label>
        <input type="text" id="tag" name="tag" value="{{ old('tag') }}">


        <label for="image"></label>
        <input type="file" id="image" name="image" accept="image/*" value="{{ old('image') }}">

        <label for="categoryid">Category:</label>
        <select name="categoryid" id="categoryid" required>
            <option value="">-- Select a Category --</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ old('categoryid') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="{{old('due_date')}}">


        <div class="rating">
            <input type="radio" name="rate" id="rate_happy" value="happy">
            <label for="rate_happy">
                <img src="/GbookImages/001-happy-face.png" alt="Happy">
            </label>

            <input type="radio" name="rate" id="rate_smile" value="smile">
            <label for="rate_smile">
                <img src="/GbookImages/004-smile.png" alt="Smile">
            </label>

            <input type="radio" name="rate" id="rate_neutral" value="neutral">
            <label for="rate_neutral">
                <img src="/GbookImages/003-confused.png" alt="Neutral">
            </label>

            <input type="radio" name="rate" id="rate_sad" value="sad">
            <label for="rate_sad">
                <img src="/GbookImages/002-sad.png" alt="Sad">
            </label>

            <input type="radio" name="rate" id="rate_angry" value="angry">
            <label for="rate_angry">
                <img src="/GbookImages/005-angry.png" alt="Angry">
            </label>
        </div>



        <button type="submit">Post Message</button>
    </form>
</body>

</html>