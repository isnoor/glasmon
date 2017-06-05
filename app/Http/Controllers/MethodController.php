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

/*use Illuminate\Http\Request;*/
use App\HoneyCore\PollingUpdate;
use App\Models\ModelsGeneral;
class MethodController extends Controller
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
        $method = Event::groupBy('method')->get()->toArray();
        
        $colorDoc =array('rgb(211, 55, 36)','rgb(0, 141, 76)', 'rgb(53, 124, 165)','rgb(219, 139, 11)', 'rgb(85, 82, 153)','rgb(57, 204, 204)' );
       
        $i=0;
        $datasets = array('data'=>array(),'backgroundColor'=>array());
        $labels = array();
        foreach ($method as $key => $value) {
            $labels[] = $value['method'];
            $count = Event::where("method", $value['method'])->count();
            $datasets['data'][]=  $count;
            $datasets['backgroundColor'][]=  $colorDoc[$i];
            $i++;
        }
        $result['datasets'] = $datasets;
        $result['labels'] = $labels;
        $result = json_encode($result);

        return $result;
    }

}
