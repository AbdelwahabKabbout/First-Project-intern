<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/Light.css')
</head>

<body>
    <h1>Welcome to Home page</h1>

    @if ($errors->any())
    @foreach ($errors->all() as $error)
    @include('components.alert', ['message' => $error, 'type' => 'alert-danger'])
    @endforeach
    @elseif(session('success'))
    @include('components.alert', ['message' => session('success'), 'type' => 'alert-success'])
    @endif

    <a href="{{ route('posts.create') }}" class="create-link">Create Post</a>


    @foreach ( $posts as $post )
    <div class="post">
        <h2>{{ $post->title }}</h2>
        <p><strong>By:</strong> {{ $post->username }}</p>
        <p>{{ $post->message }}</p>
        <div class="post-actions">
            <a href="{{ route('posts.edit', $post) }}" class="edit-link">Edit</a>
            <form action="{{ route('posts.destroy', $post) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-btn">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</body>

</html>