<?php
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Http\Controllers;

set_time_limit(0);
use App\Models\Mnemosyne\Hpfeed;
use App\Models\Glastopf\Event;
use Illuminate\Support\Facades\Input;

/*use Illuminate\Http\Request;*/
use App\HoneyCore\PollingUpdate;
use App\Models\ModelsGeneral;
class SensorController extends Controller
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

    public function index(){
        $limit = Input::has('length')? Input::get('length'):15;
        $offset = Input::has('start')? Input::get('start'):0;
        $glastopf = Hpfeed::where("channel","glastopf.events")->groupBy('ident')->take((int)$limit)->skip((int)$offset)->get()->toArray();
        $result['draw']= Input::has('draw')?Input::get("draw"):1;
        $result["recordsFiltered"]= $result["recordsTotal"]=Hpfeed::where("channel","glastopf.events")->distinct('ident')->get()->count();
        $data = array();
        foreach ($glastopf as $key => $value) {
            $event = Event::where("ident",$value['ident']);
            $ip = $event->first();
            $ip = $ip->destination;
            $count = $event->count();
            $data[]= array(
                $value['ident'],
                $ip,
                $count
                );
        }
        $result['data'] = $data;
        $result = json_encode($result);

        return $result;
    }

}
