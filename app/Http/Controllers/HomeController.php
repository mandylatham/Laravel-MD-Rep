<?php

namespace App\Http\Controllers;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function fcm()
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
                            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = "d96gh4meEfc:APA91bH5EXcvzMlVlSmk6z9iEs6-E4S3fgcylQeRFCS1Vo3v-cH8DUzdZuX5811YSR-22XmlZ1tPsMSBvXKyKyky8Y-QBVgeObmWFi2C0PAIWW_MBf8sEkK_IbWSTPPV9uxKzQlnCD_S";

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        var_dump($downstreamResponse->numberSuccess());
        //print_r($downstreamResponse->numberFailure());
        //print_r($downstreamResponse->numberModification());

       /* //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens*/
    }
}
