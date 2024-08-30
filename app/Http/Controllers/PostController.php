<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = $request->user()->posts()
            ->with('tags')
            ->orderBy('pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        return PostResource::collection($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'required|image',
            'pinned' => 'required|boolean',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        $post = $request->user()->posts()->create($validated);
        $post->tags()->attach($validated['tags']);

        return new PostResource($post);
    }

    public function show(Request $request, Post $post)
    {
        $this->authorize('view', $post);
        return new PostResource($post);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'cover_image' => 'nullable|image',
            'pinned' => 'required|boolean',
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($request->hasFile('cover_image')) {
            Storage::disk('public')->delete($post->cover_image);
            $validated['cover_image'] = $request->file('cover_image')->store('covers', 'public');
        }

        $post->update($validated);
        $post->tags()->sync($validated['tags']);

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->json(null, 204);
    }

    public function deleted(Request $request)
    {
        $deletedPosts = $request->user()->posts()->onlyTrashed()->get();
        return PostResource::collection($deletedPosts);
    }

    public function restore(Request $request, $id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $post);
        $post->restore();
        return new PostResource($post);
    }
}
