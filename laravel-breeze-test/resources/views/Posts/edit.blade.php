<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/Light.css')
</head>

<body>
    <h1>Update Post</h1>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <a href="{{ route('posts.index') }}" class="back-link">Back to Posts</a>

    <form action="{{ route('posts.update', $post) }}" method="POST">
        @csrf
        @method('PUT')

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="{{ old('username', $post->username) }}"><br><br>

        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}"><br><br>

        <label for="message">Message:</label>
        <textarea id="message" name="message">{{ old('message', $post->message) }}</textarea><br><br>

        <button type="submit">Update Post</button>
    </form>

</body>

</html>