<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace("Api")->group(function () {

	Route::post('register', "UserController@register");
	Route::post('social-register', "UserController@socialRegister");
	Route::post('social-login', "UserController@socialCheck");
	Route::post('user/send-email-verify', "UserController@sendConfirmationEmailOtp");
	Route::post('user/verify-email', "UserController@checkVerificationToken");

	Route::post('login', "UserController@login");

	Route::post('reset-password', "ForgotPasswordController@sendResetLinkEmail");
	Route::post('check-reset-token', "ForgotPasswordController@checkResetToken");



	Route::middleware('auth:api')->group(function () {


		Route::post('update-password', "UserController@updatePassword");

		Route::get('profile', "UserController@profile");
		Route::post('profile', "UserController@profileUpdate");
		Route::post('change-password', "UserController@changePassword");
		Route::post('update-fcm', "UserController@fcmUpdate");
		Route::post('logout', "UserController@logout");
		Route::post('user-block', "UserController@userBlock");
		Route::post('block-list', "UserController@blockList");
		Route::post('user/location', "UserController@updateLocation");

		//Follow Routes
		Route::post('user/list', "FollowController@getUsersList");
		Route::post('user/follow', "FollowController@follow");
		Route::post('user/follower-followed', "FollowController@followerList");

		//Post Related Routes
		Route::post('user-post', "UserPostController@index");
		Route::post('user/posts', "UserPostController@getPosts");
		Route::post('user/attachments', "UserPostController@getPostAttachments");
		Route::post('create-post', "UserPostController@create");
		Route::post('update-post', "UserPostController@update");
		Route::post('destroy-post', "UserPostController@destroy");
		Route::post('post/like', "UserPostController@like");
		Route::post('user/wall', "UserPostController@wall");
		Route::post('post/share',"UserPostController@share");
		Route::post('post/likes', "UserPostController@getLike");
		Route::post('wall-post', "UserPostController@wall");
		Route::post('post-hide', "UserPostController@hidePost");
		Route::post('post/video-count', "UserPostController@videoCount");

		Route::post('user/notifications', "NotificationController@getNotifications");
		Route::post('user/notification', "NotificationController@updateNotification");

		//Event Related Routes
		Route::post('event/book', "EventController@bookEvent");
		Route::post('event/booked-list', "EventController@getBookedEvent");
		Route::post('event/detail', "EventController@getEventDetail");
		Route::post('event-cancelled', "EventController@eventCancelled");
		Route::post('event/list', "EventController@getList");

		//Comment Routes
		Route::post('post/comment', "CommentController@create");
		Route::post('post/comment/destroy', "CommentController@destroy");
		Route::post('post/comment/like', "CommentController@like");
		Route::post('post/comment/likes', "CommentController@getLikes");
		Route::post('post/comment/update', "CommentController@update");
		Route::post('post/comments', "CommentController@getList");

		//Dashboard Routes
		Route::post('user/dashboard/update', "DashboardController@updateDashboardLayers");
		Route::get('user/dashboard/edit', "DashboardController@getDashboardLayers");
		Route::get('user/dashboard', "DashboardController@getDashboardImage");


		//Chat Message
		Route::get('chats', "ChatController@getChats");
		Route::get('user/chats', "ChatController@getChatMessage");
		Route::post('user/chat', "ChatController@message");

		Route::post('chat/delete', "ChatController@destroy");
		Route::post('chat/clear', "ChatController@clearChat");
		Route::post('chat/group/delete', "ChatController@removeGroup");


	});
		//Broadcast Auth
		Route::post('broadcast/auth', "BroadcastAuthController@auth");
		
		
		Route::get('event/types', "EventController@getEventTypes");
		
		Route::post('stricker-create', "DashboardController@create");
		Route::post('stricker-type-create', "DashboardController@createStrickerType");
		Route::post('strickers-type', "DashboardController@stickersType");
		Route::post('strickers', "DashboardController@getStickers");
		Route::post('interior-strickers', "DashboardController@interiorSticker");

});
