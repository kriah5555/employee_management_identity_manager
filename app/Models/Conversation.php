<?php

namespace App\Models;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';
    protected $primaryKey = 'id';
    protected $fillable = ['type', 'created_by', 'updated_by'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_users', 'conversation_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }

    public function deleteMessages()
    {
        // Delete all messages related to this conversation
        $this->messages()->delete();
    }

    public function deleteConversation()
    {
        // Delete related messages
        $this->deleteMessages();

        // Delete the conversation
        $this->delete();
    }

}
