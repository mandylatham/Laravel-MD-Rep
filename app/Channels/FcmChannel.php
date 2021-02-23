<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use DB;
use Illuminate\Support\Facades\Log;
use App\DeviceToken;

class FcmChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFCM($notifiable);

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($message["title"]);
        $notificationBuilder->setBody($message["message"])
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($message["data"]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        // You must change it to get your tokens
        $tokens = DeviceToken::select(['code'])->whereIn("user_id", $message["users"])->get();
            
        if($tokens->count() > 0){
            $downstreamResponse = FCM::sendTo($tokens->pluck('code')->toArray(), $option, $notification, $data);
            $this->handleError($downstreamResponse);
            Log::debug("Token Success");
            Log::debug($downstreamResponse->numberSuccess());
            Log::debug("Token Failure");
            Log::debug($downstreamResponse->numberFailure());
        }
        
        /*$downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        $downstreamResponse->numberModification();

        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();*/
       
        
    }

    private function handleError($response)
    {
        $tokens = $response->tokensToDelete();
        DeviceToken::whereIn("code", $tokens)->delete();
    }
}