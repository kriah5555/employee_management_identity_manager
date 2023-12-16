<?php

namespace App\Http\Controllers;

use App\Http\Rules\ChatRequest;
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

    public function createConversation(ChatRequest $request)
    {
        $conversation = $this->chatService-> checkConversation($request);
         return $conversation;
    }


    public function getMessagesInConversationFormat(ChatRequest $conversationId)
    {
        $formattedMessages = $this->chatService->getMessagesInConversationFormat($conversationId);
        return $formattedMessages;
    }

    public function sendMessage(ChatRequest $request)
    {
        $response = $this->chatService->sendMessage( $request);
        return $response;
    }

    public function deleteConversation(ChatRequest $request)
    {
       $response =  $this->chatService->deleteConversation($request);
       return $response;

    }


    public function deleteMessage(ChatRequest $request)
    {
        $response = $this->chatService->deleteMessage($request);
        return $response;

    }


}
