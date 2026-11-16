<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin-orders-channel', function ($user) {
    return $user !== null && ($user->role === 'admin');
});

Broadcast::channel('user-orders-channel.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});