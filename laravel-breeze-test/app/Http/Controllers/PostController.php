<?php

namespace App\Http\Controllers;

use App\Models\Post;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function index(Request $request)
    {

        $filters = $request->validate([
            'username' => 'sometimes|string|max:255',
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string|max:1000',
            'created_at' => 'sometimes|date',
        ]);

        $query = Post::query();


        if ($request->has('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('message')) {
            $query->where('message', 'like', '%' . $request->message . '%');
        }

        if ($request->has('created_at')) {
            $query->whereDate('created_at', $request->created_at);
        }

        $posts = $query->get();

        return view('Posts.index', compact('posts'));
    }



    public function create()
    {
        return view('Posts.create');
    }


    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'username' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        Post::create($incomingFields);
        return redirect()->route('posts.index')->with('success', 'Post created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('Posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $incomingFields = $request->validate([
            'username' => 'required|string|max:255',
            'title'    => 'required|string|max:255',
            'message'  => 'required|string',
        ]);

        $post->update($incomingFields);

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post updated successfully!');
    }



    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()
            ->route('posts.index')
            ->with('success', 'Post deleted successfully!');
    }
}
