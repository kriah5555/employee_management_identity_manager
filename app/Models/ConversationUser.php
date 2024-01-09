<?php

namespace App\Models;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConversationUser extends Model
{
    use HasFactory;
    protected $table = 'conversation_users';
    protected $primaryKey = 'id';
    protected $fillable = ['conversation_id','user_id', 'created_by', 'updated_by'];


    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }
}
