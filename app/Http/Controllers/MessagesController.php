<?php

namespace App\Http\Controllers;

use App\Events\MessageSentEvent;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessagesResource;
use App\Models\Conversation;
use App\Models\Messages;
use App\Services\MessagesServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessagesController extends Controller
{
    /**
     * Display the messages of a conversation.
     */
    public function index(Conversation $conversation): JsonResponse 
    {
        //
        $this->authorize('view', $conversation);
        $messages = $conversation
            ->messages()
            ->with('user')
            ->latest()
            ->paginate(30);

        return response()->json([
            'data' => MessagesResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    /**
     * Store a new message.
     */
    public function store(MessageRequest $request ,MessagesServices $messagesServices ,Conversation $conversation): JsonResponse 
    {
        //
        $this->authorize('sendMessage', $conversation);
        $authUser = $request->user();
        $validated = $request->validated();
        $mediaFile = $request->file('media');

        $message = $messagesServices->createNewMessage($conversation ,$validated ,$authUser ,$mediaFile);

        broadcast(new MessageSentEvent($message))->toOthers();

        return response()->json([
            'data' => new MessagesResource($message),
        ], 201);
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Request $request ,MessagesServices $messagesServices ,Messages $message): JsonResponse 
    {
        //
        $this->authorize('view' ,$message->conversation);
        $authUser = $request->user(); 

        $messagesServices->maskMsgAsRead($message ,$authUser);

        return response()->json([
            'data' => new MessagesResource(
                $message->load('user')
            ),
        ]);
    }

    /**
     * Delete a message.
     */
    public function destroy(MessagesServices $messagesServices ,Messages $message): JsonResponse 
    {
        //
        $this->authorize('delete' ,$message);

        $messagesServices->deleteMediaIfExists($message);
        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully.',
        ]);
    }
}