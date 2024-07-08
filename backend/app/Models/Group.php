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
}
