<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;


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

            // Validation rules for user IDs
            $rules = [
                'senderId' => 'required|integer|exists:users,id',
                'receiverIds' => 'required|array',
                'receiverIds.*' => 'integer|exists:users,id',
            ];

            $customMessages = [
                'senderId.required' => 'Sender ID is required.',
                'senderId.integer' => 'Sender ID must be an integer.',
                'senderId.exists' => 'Sender ID does not exist.',
                'receiverIds.required' => 'Receiver IDs are required.',
                'receiverIds.array' => 'Receiver IDs must be an array.',
                'receiverIds.*.integer' => 'Receiver IDs must be integers.',
                'receiverIds.*.exists' => 'One or more receiver IDs do not exist.',
            ];

            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                return response()->json(['status' => false, 'message' => $errorMessages[0]], 400);
            }

            // Check if all user IDs exist in the User table
            $validUserIds = User::whereIn('id', $userIds)->pluck('id')->all();

            if (count($validUserIds) !== count($userIds)) {
                return response()->json(['status' => false, 'error' => 'Invalid sender or receiver IDs provided'], 400);
            }

            // If no existing conversation is found, create a new one
            $conversation = new Conversation();
            $conversation->type = 1;
            $conversation->created_by = $senderId;
            $conversation->updated_by = $senderId;
            $conversation->save();
            $conversation->users()->attach($validUserIds);

            return response()->json(['status' => true, 'message' => $conversation], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }






    public function getMessagesInConversationFormat($conversationId)
    {
        try {
            // Validation rules for conversationId
            $rules = [
                'conversationId' => 'required|integer|exists:conversations,id',
            ];

            $customMessages = [
                'conversationId.required' => 'Conversation ID is required.',
                'conversationId.integer' => 'Conversation ID must be an integer.',
                'conversationId.exists' => 'Conversation ID does not exist.',
            ];

            $validator = Validator::make(['conversationId' => $conversationId], $rules, $customMessages);

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                return response()->json(['status' => false, 'message' => $errorMessages[0]], 400);
            }

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

            return response()->json(['status' => true, 'messages' => $formattedMessages], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }




    public function sendMessage($request)
    {
        try {
            // Validation rules for sender_id, conversation_id, and receiver_ids
            $rules = [
                'sender_id' => 'required|integer|exists:users,id',
                'conversation_id' => 'required|integer|exists:conversations,id',
                'content' => 'required|string',
            ];

            $customMessages = [
                'sender_id.required' => 'Sender ID is required.',
                'sender_id.integer' => 'Sender ID must be an integer.',
                'sender_id.exists' => 'Sender ID does not exist.',
                'conversation_id.required' => 'Conversation ID is required.',
                'conversation_id.integer' => 'Conversation ID must be an integer.',
                'conversation_id.exists' => 'Conversation ID does not exist.',
                'receiver_ids.required' => 'Receiver IDs are required.',
                'content.required' => 'Message content is required.',
                'content.string' => 'Message content must be a string.',
            ];

            $validator = Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                return response()->json(['status' => false, 'message' => $errorMessages[0]], 400);
            }

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

            return response()->json(['status' => true, 'message' => $messages], 200);


            // Example: Handle attachments, create messages, and return the response
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }







    public function deleteConversation($conversationId)
    {
        try {
            // Validation rules for conversationId
            $rules = [
                'conversationId' => 'required|integer|exists:conversations,id',
            ];

            $customMessages = [
                'conversationId.required' => 'Conversation ID is required.',
                'conversationId.integer' => 'Conversation ID must be an integer.',
                'conversationId.exists' => 'Conversation ID does not exist.',
            ];

            $validator = Validator::make(['conversationId' => $conversationId], $rules, $customMessages);

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                return response()->json(['status' => false, 'messages' => $errorMessages[0]], 400);
            }

            $conversation = Conversation::find($conversationId);

            if (!$conversation) {
                return response()->json(['status' => false, 'messages' => 'Conversation is not available'], 404);
            }

            $conversation->deleteConversation();

            return response()->json(['status' => true, 'message' => "$conversationId deleted"], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }


    public function deleteMessage($messageId)
    {
        try {
            // Validation rules for messageId
            $rules = [
                'messageId' => 'required|integer|exists:messages,id',
            ];

            $customMessages = [
                'messageId.required' => 'Message ID is required.',
                'messageId.integer' => 'Message ID must be an integer.',
                'messageId.exists' => 'Message ID does not exist.',
            ];

            $validator = Validator::make(['messageId' => $messageId], $rules, $customMessages);

            if ($validator->fails()) {
                $errorMessages = $validator->errors()->all();
                return response()->json(['status' => false, 'error' => $errorMessages[0]], 400);
            }

            $message = Message::find($messageId);

            if (!$message) {
                return response()->json(['status' => false, 'error' => 'Message is not available'], 404);
            }

            $message->delete();

            return response()->json(['status' => true, 'message' => "$messageId deleted"], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
