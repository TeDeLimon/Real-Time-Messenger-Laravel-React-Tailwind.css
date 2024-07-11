<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id1',
        'user_id2',
        'last_message_id'
    ];

    /**
     * Relationship with Message model, inverse one to one because a conversation has only one last message
     */
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Relationship with User model, inverse one to many because a conversation has only one user1
     */
    public function user1()
    {
        return $this->belongsTo(User::class, 'user_id1');
    }

    /**
     * Relationship with User model, inverse one to many because a conversation has only one user2
     */
    public function user2()
    {
        return $this->belongsTo(User::class, 'user_id2');
    }

    /**
     * Get conversations for the sidebar for an authenticated user
     * 
     * @param User $user The authenticated user
     * 
     * @return Collection The conversations for the sidebar
     */
    public static function getConversationsForSidebar(User $user)
    {
        // Get all users where the current user is involved in a conversation
        $users = User::getUsersExceptUser($user);
        // Get all groups where the current user is a member
        $groups = Group::getGroupsForUser($user);

        return $users
            ->map(fn (User $user) => $user->toConversationArray())
            ->concat($groups->map(fn (Group $group) => $group->toConversationArray()));
    }
}
