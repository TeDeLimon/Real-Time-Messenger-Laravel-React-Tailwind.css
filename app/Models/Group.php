<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'last_message_id'
    ];

    /**
     * Relationship with User model, many to many
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users');
    }

    /**
     * Relationship with Message model, one to many
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relationship with User model, inverse one to one because a group has only one owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get Groups where the user is a member, with the last message and date
     * 
     * @param User $user The authenticated user
     * 
     * @return Collection The groups where the user is a member
     */
    public static function getGroupsForUser(User $user)
    {
        $query = self::select(['groups.*', 'messages.message as last_message', 'messages.created_at as last_message_date'])
            ->join('group_users', 'group_users.group_id', '=', 'groups.id')
            ->leftJoin('messages', 'messages.id', '=', 'groups.last_message_id')
            ->where('group_users.user_id', $user->id)
            ->orderBy('messages.created_at', 'desc')
            ->orderBy('groups.name');

        return $query->get();
    }

    /**
     * Similar to the toConversationArray method in the User model
     */
    public function toConversationArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'is_group' => true,
            'is_user' => false,
            'owner_id' => $this->owner_id,
            'users' => $this->users,
            'users_ids' => $this->users->pluck('id'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_message' => $this->last_message,
            'last_message_date' => $this->last_message_date
        ];
    }
}
