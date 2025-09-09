@php
$themeData = view('components.themeToggle')->render();
$currentTheme = config('theme.current', 'light');
//$categories = App\Models\GuestCategory::whereNull('deleted_at')->orderBy('name')->get();
@endphp


<!DOCTYPE html>
<html>

<head>
    <title>Edit Message - Guestbook</title>
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
    <h1>Edit Message</h1>

    <a href="{{ route('guestbook.index') }}">&larr; Back to Guestbook</a>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <form action="{{ route('guestbook.update', $entry->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="{{ old('name', $entry->name) }}">

        <label for="email">Email (optional):</label>
        <input type="text" id="email" name="email" value="{{ old('email', $entry->email) }}">

        <label for="message">Message:</label>
        <textarea id="message" name="message">{{ old('message', $entry->message) }}</textarea>

        <label for="tag">Tag:</label>
        <input type="text" id="tag" name="tag" value="{{ old('tag' , $entry->tag) }}">


        <label for="image">Upload Image</label>
        <input type="file" id="image" name="image" accept="image/*">

        @if($entry->image)
        <p>Current Image:</p>
        <img src="data:{{ $entry->image_type }};base64,{{ base64_encode($entry->image) }}"
            alt="Current Image"
            style="max-width: 200px; max-height: 200px;">

        <button type="submit" name="clearImage" value="1">Clear Image</button>

        @endif

        <label for="categoryid">Category:</label>
        <select name="categoryid" id="categoryid" required>
            <option value="">-- Select a Category --</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}"
                {{ old('categoryid', $entry->category->id) == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="{{old('due_date',$entry->due_date)}}">



        <div class="rating">
            <input type="radio" name="rate" id="rate_happy" value="happy"
                {{ old('rate', $entry->rate) === 'happy' ? 'checked' : '' }}>
            <label for="rate_happy">
                <img src="/GbookImages/001-happy-face.png" alt="Happy">
            </label>

            <input type="radio" name="rate" id="rate_smile" value="smile"
                {{ old('rate', $entry->rate) === 'smile' ? 'checked' : '' }}>
            <label for="rate_smile">
                <img src="/GbookImages/004-smile.png" alt="Smile">
            </label>

            <input type="radio" name="rate" id="rate_neutral" value="neutral"
                {{ old('rate', $entry->rate) === 'neutral' ? 'checked' : '' }}>
            <label for="rate_neutral">
                <img src="/GbookImages/003-confused.png" alt="Neutral">
            </label>

            <input type="radio" name="rate" id="rate_sad" value="sad"
                {{ old('rate', $entry->rate) === 'sad' ? 'checked' : '' }}>
            <label for="rate_sad">
                <img src="/GbookImages/002-sad.png" alt="Sad">
            </label>

            <input type="radio" name="rate" id="rate_angry" value="angry"
                {{ old('rate', $entry->rate) === 'angry' ? 'checked' : '' }}>
            <label for="rate_angry">
                <img src="/GbookImages/005-angry.png" alt="Angry">
            </label>
        </div>




        <button type="submit">Update Message</button>
    </form>
</body>

</html>