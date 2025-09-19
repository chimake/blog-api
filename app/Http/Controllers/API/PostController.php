<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PostController extends Controller
{

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $posts = Post::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => PostResource::collection($posts->items()),
                    'pagination' => [
                        'current_page' => $posts->currentPage(),
                        'last_page' => $posts->lastPage(),
                        'per_page' => $posts->perPage(),
                        'total' => $posts->total(),
                        'from' => $posts->firstItem(),
                        'to' => $posts->lastItem(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'body' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post = Post::create([
                'title' => $request->title,
                'body' => $request->body,
                'user_id' => $request->user()->id,
            ]);

            $post->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => [
                    'post' => new PostResource($post)
                ]
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Post $post): \Illuminate\Http\JsonResponse
    {
        try {
            $post->load('user');

            return response()->json([
                'success' => true,
                'data' => [
                    'post' => new PostResource($post)
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Post $post): \Illuminate\Http\JsonResponse
    {
        try {
            // Check if the authenticated user owns this post
            if ($post->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own posts'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'body' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $post->update([
                'title' => $request->title,
                'body' => $request->body,
            ]);

            $post->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => [
                    'post' => new PostResource($post)
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, Post $post): \Illuminate\Http\JsonResponse
    {
        try {
            // Check if the authenticated user owns this post
            if ($post->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own posts'
                ], 403);
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function myPosts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $posts = Post::where('user_id', $request->user()->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => PostResource::collection($posts->items()),
                    'pagination' => [
                        'current_page' => $posts->currentPage(),
                        'last_page' => $posts->lastPage(),
                        'per_page' => $posts->perPage(),
                        'total' => $posts->total(),
                        'from' => $posts->firstItem(),
                        'to' => $posts->lastItem(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve your posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'q' => 'required|string|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required',
                    'errors' => $validator->errors()
                ], 422);
            }

            $searchTerm = $request->get('q');

            $posts = Post::with('user')
                ->search($searchTerm)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => [
                    'posts' => PostResource::collection($posts->items()),
                    'search_term' => $searchTerm,
                    'pagination' => [
                        'current_page' => $posts->currentPage(),
                        'last_page' => $posts->lastPage(),
                        'per_page' => $posts->perPage(),
                        'total' => $posts->total(),
                        'from' => $posts->firstItem(),
                        'to' => $posts->lastItem(),
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search posts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
