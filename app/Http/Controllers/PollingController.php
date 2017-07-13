<?php
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Http\Controllers;

set_time_limit(0);
use App\Models\Glastopf\Event;

use Illuminate\Support\Facades\Input;
use DateTime;
use App\HoneyCore\PollingUpdate;
class PollingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function polling(){
        $pollingUpdate = new PollingUpdate();
        $count = $pollingUpdate->doPolling();

        $count = json_encode($count);
        return $count;
    }

    public function pollingCount(){
        $this->polling();
        if($_POST["timestamp"]!="undefined"){
            $timestamp = DateTime::createFromFormat('Y-m-d H:i:s', Input::get("timestamp"));
            /*$events = Event::where('timestamp', '>',$timestamp)->get();
            $count = count($events);*/
            $count = Event::where('timestamp', '>',$timestamp)->count();
        }else{
            $count = 0;
        }

        $count = json_encode(array("result"=>true, "count"=>$count));
        return $count;   
    }

}
