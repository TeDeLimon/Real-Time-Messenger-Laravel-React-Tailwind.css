<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'avatar',
        'name',
        'email',
        'email_verified_at',
        'password',
        'is_admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship with Message model, many to many
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_users');
    }

    /**
     * Get all conversations, last message and user info where the user is involved
     * 
     * @param User $user The authenticated user
     * 
     * @return Collection The conversations for the sidebar
     */
    public static function getUsersExceptUser(User $user)
    {
        $userId = $user->id;
        /**
            Select all information about users, the last message and the date of the last message
            where the user is not the authenticated user
            and when the authenticated user is not an admin, the user is not blocked
            left joined with the conversations table using the user_id1 and user_id2 columns
            where the user_id1 is the authenticated user and the user_id2 is the other user id
            or the user_id2 is the authenticated user and the user_id1 is the other user id

         */
        $query = User::select(['users.*', 'messages.message as last_message', 'messages.created_at as last_message_date'])
            ->where('users.id', '!=', $userId)
            ->when(!$user->is_admin, function ($query) {
                $query->whereNull('users.blocked_at');
            })
            ->leftJoin('conversations', function ($join) use ($userId) {
                $join->on('conversations.user_id1', '=', 'users.id')
                    ->where('conversations.user_id2', '=', $userId)
                    ->orWhere(function ($query) use ($userId) {
                        $query->on('conversations.user_id2', '=', 'users.id')
                            ->where('conversations.user_id1', '=', $userId);
                    });
            })
            ->leftJoin('messages', 'messages.id', '=', 'conversations.last_message_id')
            // We order by the block_at column, blocked_users must be on the bottom
            // If the user is blocked, the block_at column will have a value, otherwise it will be null
            ->orderByRaw('IFNULL(users.blocked_at, 1)')
            ->orderBy('messages.created_at', 'desc')
            ->orderBy('users.name');

        // dd($query->toSql()); // Debugging the query

        return $query->get();
    }

    /**
     * Similar to a resource, this method returns an array with the conversation information
     */
    public function toConversationArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_group' => false,
            'is_user' => true,
            'is_admin' => (bool) $this->is_admin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'blocked_at' => $this->blocked_at,
            'last_message' => $this->last_message,
            'last_message_date' => $this->last_message_date
        ];
    }
}
