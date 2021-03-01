<?php

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

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('fun_private_{from_user}_{to_user}', function ($user, $from_user, $to_user) {
	return (int) $user->id === (int) $from_user || (int) $user->id === (int) $to_user;
});


Broadcast::channel('fun_user_{id}', function ($user, $id) {
	return (int) $user->id === (int) $id;
});
