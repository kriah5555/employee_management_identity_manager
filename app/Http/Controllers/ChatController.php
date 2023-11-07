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


    public function getMessagesInConversationFormat(Request $conversationId)
    {
        $formattedMessages = $this->chatService->getMessagesInConversationFormat($conversationId);
        return $formattedMessages;
    }

    public function sendMessage(Request $request)
    {
        $response = $this->chatService->sendMessage( $request);
        return $response;
    }

    public function deleteConversation(Request $request)
    {
       $response =  $this->chatService->deleteConversation($request);
       return $response;

    }


    public function deleteMessage(Request $request)
    {
        $response = $this->chatService->deleteMessage($request);
        return $response;

    }


}
