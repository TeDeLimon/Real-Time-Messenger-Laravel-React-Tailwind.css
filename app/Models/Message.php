<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'sender_id',
        'receiver_id',
        'group_id'
    ];

    /**
     * Relationship with User model, inverse one to many because a message has only one sender
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship with User model, inverse one to many because a message has only one receiver
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Relationship with Group model, inverse one to many because a message belongs to only one group
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Relationship with MessageAttachment model, one to many because a message can have multiple attachments
     */
    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class);
    }
}
