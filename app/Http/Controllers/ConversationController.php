<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Models\Conversation;
use App\Services\ConversationServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    /**
     * Display the authenticated user's conversations.
     */
    public function index(Request $request): JsonResponse
    {
        $conversations = $request->user()
            ->conversations()
            ->with(['users','messages' 
                    => fn ($query) => $query
                    ->latest()
                    ->limit(1)
                ])
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => ConversationResource::collection($conversations),
            'meta' => [
                'current_page' => $conversations->currentPage(),
                'last_page' => $conversations->lastPage(),
                'per_page' => $conversations->perPage(),
                'total' => $conversations->total(),
            ],
        ],200);
    }

    /**
     * Create a new conversation.
     */
    public function store(ConversationRequest $request ,ConversationServices $conversationServices): JsonResponse 
    {
        $user = $request->user();
        $validated = $request->validated();

        $conversation = $conversationServices->createConversation($validated ,$user);

        return response()->json([
            'data' => $conversation,
        ], 201);
    }

    /**
     * Display a conversation.
     */
    public function show(Conversation $conversation): JsonResponse 
    {
        $this->authorize('view' ,$conversation);

        $conversation->load('users');

        return response()->json([
            'data' => $conversation,
        ],200);
    }

    /**
     * Remove the authenticated user from the conversation.
     */
    public function leave(Request $request ,Conversation $conversation): JsonResponse 
        {
        $this->authorize('leave' ,$conversation);

        $conversation->users()->detach($request->user()->id);

        return response()->json([
            'message' => 'You left the conversation successfully.',
        ],200);
    }

    /**
     * Delete the conversation.
     */
    public function destroy(Conversation $conversation): JsonResponse 
    {
        $this->authorize('delete' ,$conversation);

        $conversation->delete();

        return response()->json([
            'message' => 'Conversation deleted successfully.',
        ],200);
    }
}