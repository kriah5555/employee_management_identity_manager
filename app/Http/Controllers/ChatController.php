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

    public function getConversationIDs(ChatRequest $request)
    {
        $conversationIDS = $this->chatService-> getConversationIDs($request);
        return $conversationIDS;
    }


    public function getMessagesInConversationFormat(ChatRequest $request)
    {
        // dd($request);
        $formattedMessages = $this->chatService->getMessagesInConversationFormat($request);
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
