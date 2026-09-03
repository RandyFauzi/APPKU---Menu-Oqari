<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('shop.{shopId}.orders', function ($user, $shopId) {
    return (int) $user->shop_id === (int) $shopId && $user->can('view-orders');
});
