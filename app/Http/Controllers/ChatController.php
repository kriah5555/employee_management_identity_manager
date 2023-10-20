<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChatService;



class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function createConversation(Request $request)
    {
        $conversation = $this->chatService-> checkConversation($request);
        return response()->json(['conversation_id' => $conversation->id]);
    }
    public function getMessages($conversationId)
    {
        $messages = $this->chatService->getMessages($conversationId);
        return response()->json(['messages' => $messages]);
    }

    public function getMessagesInConversationFormat($conversationId)
    {
        $formattedMessages = $this->chatService->getMessagesInConversationFormat($conversationId);
        return response()->json(['conversation' => $formattedMessages]);
    }

    public function sendMessage(Request $request)
    {
        $response = $this->chatService->sendMessage( $request);
        return response()->json($response);
    }

    public function deleteConversation($conversationId)
    {
        $this->chatService->deleteConversation($conversationId);
        return response()->json(['message' => 'Conversation deleted']);
    }

    public function deleteMessage($messageId)
    {
        $this->chatService->deleteMessage($messageId);
        return response()->json(['message' => 'message deleted']);
    }


}
