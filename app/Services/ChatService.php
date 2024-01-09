<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User\User;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;


class ChatService
{

    public function getConversationIDs($request)
    {
        try {
            $senderId = $request->input('sender_id');
            $receiverIds = $request->input('receiver_ids');

            // Check if all receiver IDs exist in the User table
            $validReceiverIds = User::whereIn('id', $receiverIds)->pluck('id')->all();

            if (count($validReceiverIds) !== count($receiverIds)) {
                return response()->json(['success' => false, 'message' => 'Invalid receiver IDs provided'], 400);
            }

            // Remove senderId from the receiverIds array
            $receiverIds = array_diff($receiverIds, [$senderId]);

            // Check if there's any conversation involving the sender
            $senderConversations = Conversation::whereHas('users', function ($query) use ($senderId) {
                $query->where('user_id', $senderId);
            })->get();

        
            $conversationIds = [];

            foreach ($receiverIds as $receiverId) {
                // Check if there's an existing conversation between sender and receiver
                $conversation = $senderConversations->first(function ($conversation) use ($receiverId) {
                    return $conversation->users->contains('id', $receiverId);
                });

                // If conversation exists, add its ID to the array
                if ($conversation) {
                    $conversationIds[] = $conversation->id;
                }
            }

            return response()->json(['success' => true, 'conversation_ids' => $conversationIds], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function getMessagesInConversationFormat($request)
    {
        // dd($request->toarray());
        try {
            $conversationID = $request->input('conversation_id');
            // $receiverId = $request->input('receiver_id');

            // // Find the conversation based on sender and receiver IDs
            // $conversation = DB::table('conversation_users as cu1')
            //     ->join('conversation_users as cu2', 'cu1.conversation_id', '=', 'cu2.conversation_id')
            //     ->where('cu1.user_id', $senderId)
            //     ->where('cu2.user_id', $receiverId)
            //     ->select('cu1.conversation_id')
            //     ->first();


            if (!$conversationID) {
                return response()->json(['success' => false, 'message' => 'ConversationID is not available'], 404);
            }

            // dd($conversation->conversation_id);


            $messages = Message::where('conversation_id', $conversationID)
                ->orderBy('created_at', 'asc')
                ->get();

            $formattedMessages = [];

            foreach ($messages as $message) {
                $userName = $message->sender->username;
                $attachmentPath = $message->attachment_path;
                $fullAttachmentPath = $attachmentPath ? asset('storage/' . $attachmentPath) : null;

                $formattedMessages[] = [
                    '_id' => $message->id,
                    'conversation_id' => $message->conversation_id,
                    'name' => $userName,
                    'text' => $message->content,
                    'createdAt' => $message->created_at, // Corrected the typo here
                    'image' => $fullAttachmentPath,
                    'user' => [
                        '_id' => $message->sender_id,
                    ],
                ];
            }

            return response()->json(['success' => true, 'conversation_data' => $formattedMessages], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function sendMessage($request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();

            $attachmentPath = null;


            $senderId = $request->input('sender_id');
            $receiverIds = $request->input('receiver_ids');

            // Check if all receiver IDs exist in the User table
            $validReceiverIds = User::whereIn('id', $receiverIds)->pluck('id')->all();

            if (count($validReceiverIds) !== count($receiverIds)) {
                // Rollback the transaction if validation fails
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Invalid receiver IDs provided'], 400);
            }

            // Create messages for each receiver
            $messages = [];

            // Check if there's any conversation involving the sender
            $senderConversations = Conversation::whereHas('users', function ($query) use ($senderId) {
                $query->where('user_id', $senderId);
            })->get();

            foreach ($receiverIds as $receiverId) {
                // Check if there's an existing conversation between sender and receiver
                $conversation = $senderConversations->first(function ($conversation) use ($receiverId) {
                    return $conversation->users->contains('id', $receiverId);
                });

                if ($request->hasFile('image')) {
                    $request->validate([
                        'image' => 'file|mimes:jpg,jpeg,png',
                    ]);
                    $originalFileName = $request->file('image')->getClientOriginalName();
                    // Generate a unique identifier (timestamp-based)
                    $uniqueIdentifier = uniqid();
                    // Concatenate the unique identifier with the original file name
                    $newFileName =  $senderId . $receiverId . '_' . $uniqueIdentifier . '_' . $originalFileName;
                    // Store the file with the new name
                    $attachmentPath = $request->file('image')->storeAs('attachments', $newFileName, 'public');
                }

                // If no conversation, create a new one
                if (!$conversation) {
                    $conversation = new Conversation();
                    $conversation->type = 1;
                    $conversation->created_by = $senderId;
                    $conversation->updated_by = $senderId;
                    $conversation->save();

                    // Attach users to the conversation
                    $conversation->users()->sync([$senderId, $receiverId]);
                }

                // Create a new message
                $message = Message::create([
                    'content' => $request->input('content'),
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'conversation_id' => $conversation->id,
                    'attachment_path' => $attachmentPath,
                ]);

                $messages[] = $message;
            }

            // Commit the transaction
            DB::commit();

            // Construct the full attachment path for response
            // $fullAttachmentPath = $attachmentPath ? asset('storage/' . $attachmentPath) : null;

            return response()->json([
                'success' => true,
                // 'data'=>$messages,

                'message' => 'Messages sent successfully'
            ], 200);
        } catch (\Exception $e) {
            // Rollback the transaction on exception
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function deleteConversation($request)
    {
        try {
            DB::beginTransaction();

            $conversation = Conversation::find($request->conversation_id);
            $conversation = Conversation::find($request->conversation_id);

            if (!$conversation) {
                return response()->json(['success' => false, 'messages' => 'Conversation is not available'], 404);
            }

            // Get message IDs based on the conversation ID
            $messageIds = Message::where('conversation_id', $conversation->id)->pluck('id')->toArray();

            foreach ($messageIds as $messageId) {
                $message = Message::find($messageId);
                $this->deleteMessageImages(($message));
            }
            // dd($messageIds);
            Message::destroy($messageIds);
            // Delete all messages associated with the conversation

            // Delete the conversation
            $conversation->deleteConversation();

            DB::commit();

            return response()->json(['success' => true, 'message' => "Conversation deleted successfully"], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }




    public function deleteMessage($request)
    {

        try {

            $message = Message::find($request->message_id);

            if (!$message) {
                return response()->json(['success' => false, 'message' => 'Message is not available'], 404);
            }
            $this->deleteMessageImages(($message));

            $message->delete();


            return response()->json(['success' => true, 'message' => "Message deleted successfully"], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function deleteMessageImages($message)
    {
        // Assuming attachment_path contains the file path in the public folder
        $attachmentPath = $message->attachment_path;

        if ($attachmentPath) {
            // Delete the image file
            Storage::delete('public/' . $attachmentPath);
        }
    }
}
