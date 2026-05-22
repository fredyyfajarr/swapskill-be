<?php

namespace App\Http\Controllers\Api;

use App\Application\Posts\DTO\CreatePostInput;
use App\Application\Posts\DTO\ListPostsInput;
use App\Application\Posts\UseCases\CreatePost;
use App\Application\Posts\UseCases\DeletePost;
use App\Application\Posts\UseCases\GenerateWhatsAppLink;
use App\Application\Posts\UseCases\ListOpenPosts;
use App\Application\Posts\UseCases\RecommendPosts;
use App\Application\Posts\UseCases\UpdatePostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(Request $request, ListOpenPosts $listOpenPosts): JsonResponse
    {
        $posts = $listOpenPosts($request->user(), ListPostsInput::fromRequest($request));
        if ($posts->isEmpty()) {
            $message = "Belum ada tawaran yang pas dengan filtermu.";
        } else {
            $message = "Berhasil mengambil data Skill Board";
        }

        return response()->json([
            'message' => $message,
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'has_more' => $posts->hasMorePages()
        ]);
    }

    public function store(Request $request, CreatePost $createPost): JsonResponse
    {
        $validated = $request->validate([
            'needed_skill' => ['required', 'string', 'max:100'],
            'offered_skill' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $post = $createPost($request->user(), new CreatePostInput(
            neededSkill: $validated['needed_skill'],
            offeredSkill: $validated['offered_skill'],
            description: $validated['description'],
        ));

        return response()->json([
            'message' => 'Postingan berhasil dibuat!',
            'data' => $post
        ], 201);
    }

    public function updateStatus(Request $request, Post $post, UpdatePostStatus $updatePostStatus): JsonResponse
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed'],
        ]);

        $post = $updatePostStatus($post, $validated['status']);

        return response()->json([
            'message' => "Status postingan diubah menjadi '{$validated['status']}'",
            'data' => $post
        ]);
    }

    public function destroy(Post $post, DeletePost $deletePost): JsonResponse
    {
        Gate::authorize('delete', $post);
        $deletePost($post);

        return response()->json([
            'message' => 'Postingan barter berhasil dihapus permanen.'
        ]);
    }

    public function recommendations(Request $request, RecommendPosts $recommendPosts): JsonResponse
    {
        return response()->json([
            'message' => 'Rekomendasi jodoh barter ditemukan!',
            'data' => $recommendPosts($request->user())
        ]);
    }
}
