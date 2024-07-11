<?php

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Broadcast;

// This is the channel that we are gonna use to broadcast the messages

Broadcast::channel('online', function ($user) {
    return $user ? new UserResource($user) : null;
});
