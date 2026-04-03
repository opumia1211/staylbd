<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/*
|--------------------------------------------------------------------------
| Omnichannel conversation channel (Real-time chat)
|--------------------------------------------------------------------------
| User: can listen if conversation belongs to them (by email/session or user_id).
| Admin: can listen to any conversation when authenticated as admin.
| Guest: not allowed (use conversation token or session in production).
*/
Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    if (auth()->guard('admin')->check()) {
        return true;
    }
    if (!\Illuminate\Support\Facades\Schema::hasTable('conversations')) {
        return false;
    }
    $conversation = \App\Models\Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }
    if ($user && $conversation->user_id && (int) $conversation->user_id === (int) $user->id) {
        return true;
    }
    return false;
});
