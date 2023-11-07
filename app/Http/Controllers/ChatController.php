<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
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
         return $conversation;
    }
    public function getMessages($conversationId)
    {
        $messages = $this->chatService->getMessages($conversationId);
        return response()->json(['messages' => $messages]);
    }

    public function getMessagesInConversationFormat($conversationId)
    {
        $formattedMessages = $this->chatService->getMessagesInConversationFormat($conversationId);
        return $formattedMessages;
    }

    public function sendMessage(Request $request)
    {
        $response = $this->chatService->sendMessage( $request);
        return $response;
    }

    public function deleteConversation($conversationId)
    {
       $response =  $this->chatService->deleteConversation($conversationId);
       return $response;

    }


    public function deleteMessage($messageId)
    {
        $response = $this->chatService->deleteMessage($messageId);
        return $response;

    }


}
