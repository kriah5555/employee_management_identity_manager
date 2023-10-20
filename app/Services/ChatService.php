<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;

class ChatService
{

    public function checkConversation($request)
    {
        $senderId = (int)$request->input('senderId');
        $receiverIds = $request->input('receiverIds');

        // Ensure the sender ID is not included in the receiver IDs
        $receiverIds = array_diff($receiverIds, [$senderId]);

        // Include the sender ID in the user IDs
        $userIds = array_merge([$senderId], $receiverIds);

        // Ensure the array of user IDs is unique
        $userIds = array_unique($userIds);

        // Check if all user IDs exist in the User table
        $validUserIds = User::whereIn('id', $userIds)->pluck('id')->all();

        if (count($validUserIds) !== count($userIds)) {
            throw new \Exception('Invalid sender or receiver IDs provided');
        }

        // Check if a conversation already exists with all specified users
        $conversation = Conversation::whereHas('users', function ($query) use ($validUserIds) {
            $query->whereIn('user_id', $validUserIds);
        })->first();

        if (!$conversation) {
            // If no existing conversation is found, create a new one
            $conversation = new Conversation();
            $conversation->type = 1;
            $conversation->created_by = $senderId;
            $conversation->updated_by = $senderId;
            $conversation->save();
            $conversation->users()->attach($validUserIds);
        }

        return $conversation;
    }




    public function getMessagesInConversationFormat($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        $formattedMessages = [];

        foreach ($messages as $message) {
            $userName = $message->sender->username;

            $formattedMessages[] = [
                'name' => $userName,
                'message' => $message->content,
            ];
        }

        return $formattedMessages;
    }


    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at', 'asc')
            ->get();

        return $messages;
    }




    public function sendMessage($request)
    {
        $attachmentPath = null;

        if ($request->hasFile('file')) {
            $attachmentPath = $request->file('file')->store('attachments', 'public');
        }

        $conversationId = $request->input('conversation_id');

        $conversation = Conversation::findOrFail($conversationId);
        $senderId = $request->input('sender_id');
        $receiverIds = $request->input('receiver_ids');

        $messages = [];

        if (is_string($receiverIds)) {
            $receiverIds = explode(',', $receiverIds);
        }

        foreach ($receiverIds as $receiverId) {
            $message = Message::create([
                'content' => $request->input('content'),
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'conversation_id' => $conversation->id,
                'attachment_path' => $attachmentPath,
            ]);

            $messages[] = $message;
        }

        $response = ['messages' => $messages];

        if ($attachmentPath) {
            $response['file_path'] = asset('storage/' . $attachmentPath);
        }

        return $response;
    }



    public function deleteConversation($conversationId)
    {
        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return "Conversation is not available";
        }

        $conversation->deleteConversation();

        return "$conversationId deleted";
    }





    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);

        if (!$message) {
            return "Message is not available";
        }

        $message->delete();

        return "$messageId deleted";
    }
}
