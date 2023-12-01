<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User\User;
use App\Models\Conversation;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;


class ChatService
{

    public function checkConversation($request)
    {
        try {
            $senderId = (int)$request->input('senderId');
            $receiverIds = $request->input('receiverIds');

            // Ensure the sender ID is not included in the receiver IDs
            if (is_array($receiverIds)) {
                $receiverIds = array_diff($receiverIds, [$senderId]);
                $userIds = array_merge([$senderId], $receiverIds);

                // Ensure the array of user IDs is unique
                $userIds = array_unique($userIds);
            } else {
                return response()->json(['status' => false, 'message' => "receiver Ids must be in an array"], 400);
            }


            // Check if all user IDs exist in the User table
            $validUserIds = User::whereIn('id', $userIds)->pluck('id')->all();

            if (count($validUserIds) !== count($userIds)) {
                return response()->json(['status' => false, 'message' => 'Invalid sender or receiver IDs provided'], 400);
            }

            // If no existing conversation is found, create a new one
            $conversation = new Conversation();
            $conversation->type = 1;
            $conversation->created_by = $senderId;
            $conversation->updated_by = $senderId;
            $conversation->save();
            $conversation->users()->attach($validUserIds);

            return response()->json(['status' => true, 'message' => 'conversation created successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }






    public function getMessagesInConversationFormat($request)
    {
        try {


            $messages = Message::where('conversation_id', $request->conversation_id)
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

            return response()->json(['status' => true, 'conversation_data' => $formattedMessages], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }




    public function sendMessage($request)
    {
        try {

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


            if ($attachmentPath) {
                $response['file_path'] = asset('storage/' . $attachmentPath);
            }

            return response()->json(['status' => true, 'message' => 'Message sent successfully'], 200);


            // Example: Handle attachments, create messages, and return the response
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }







    public function deleteConversation($request)
    {
        // $conversationId=$request->conversation_id;

        try {

            $conversation = Conversation::find($request->conversation_id);

            if (!$conversation) {
                return response()->json(['status' => false, 'messages' => 'Conversation is not available'], 404);
            }

            $conversation->deleteConversation();

            return response()->json(['status' => true, 'message' => "Conversation  deleted successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }




    public function deleteMessage($request)
    {

        try {

            $message = Message::find($request->message_id);

            if (!$message) {
                return response()->json(['status' => false, 'message' => 'Message is not available'], 404);
            }

            $message->delete();

            return response()->json(['status' => true, 'message' => "Message deleted successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
