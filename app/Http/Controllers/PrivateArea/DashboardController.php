<?php

namespace App\Http\Controllers\PrivateArea;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AdminUser;
use App\AdminUserType;
use App\Event;
use App\EventBook;
use Auth;

class DashboardController extends Controller
{
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
    	$response_data["title"] = __("title.private.dashboard");
    	$response_data["count"]["user"] = User::count();
    	$userType = AdminUserType::select("id")->whereAdminType(2)->get();
    	$response_data["count"]["manager"] = AdminUser::whereIn("user_type", $userType->pluck("id")->toArray())->count();
    	$userType = AdminUserType::select("id")->whereAdminType(0)->get();
    	$response_data["count"]["club"] = AdminUser::whereIn("user_type", $userType->pluck("id")->toArray())->count();
        if($user->isClub()){
            $events = Event::whereClubUser($user->id)->get();
            $response_data["club"] = 1;
            $response_data["count"]["event"] = $events->count();
            $response_data["count"]["participant"] = EventBook::whereIn("event_id", $events->pluck("id")->toArray())->count();

        }else{
            $response_data["club"] = 0;
        	$response_data["count"]["event"] = Event::count();
            $response_data["count"]["participant"] = EventBook::count();
        }
        return view('private.dashboard')->with($response_data);
    }
}
