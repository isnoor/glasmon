<?php
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Http\Controllers;

set_time_limit(0);
use App\Models\Glastopf\Parameter;
use Illuminate\Support\Facades\Input;

/*use Illuminate\Http\Request;*/
use App\HoneyCore\PollingUpdate;
use App\Models\ModelsGeneral;
class ParameterController extends Controller
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

        $searchQuery = Input::has('value')?Input::get('value'):'all';

        $result['draw']= Input::has('draw')?Input::get("draw"):1;

        $data = array();
        if($searchQuery!=="all" && $searchQuery!=="All"){
            $parameters = Parameter::raw()->aggregate([
                ['$match'  => ['pattern'=> $searchQuery]], 
                ['$unwind' => '$event'],
                [ '$group' => [
                   "_id" => '$_id',
                   "count" => [ '$sum' => '$event.count' ]
               ]],
               [  '$sort' =>[ 'count'=>-1, 'pattern' =>1] ],
               [  '$skip' => (int)$offset],
               [  '$limit' => (int)$limit]
            ]);
            $result["recordsFiltered"]= $result["recordsTotal"]= Parameter::where('pattern',$searchQuery)->count();
        }else{
            $parameters = Parameter::raw()->aggregate([
                ['$unwind' => '$event'],
                [ '$group' => [
                   "_id" => '$_id',
                   "count" => [ '$sum' => '$event.count' ]
               ]],
               [  '$sort' =>[ 'count'=>-1, 'pattern' =>1] ],
               [  '$skip' => (int)$offset],
               [  '$limit' => (int)$limit]
            ]);

            $result["recordsFiltered"]= $result["recordsTotal"]= Parameter::count();
        }

        $data = array();
        $no=$offset;
        foreach ($parameters as $key => $value) {
            $parameter = Parameter::find($value['_id']);
            $no++;
            if(is_array($parameter->pattern)){
                $pattern = implode(", ", $parameter->pattern);
            }else{
                $pattern = $parameter->pattern;
            }

            $data[]= array(
                $no,
                $parameter->parameter,
                $pattern ,
                $value['count']
                );
        }
        $result['data'] = $data;
        $result = json_encode($result);

        return $result;
    }

    public function filterList(){
        $parameters = Parameter::distinct('pattern')->get()->toArray();
        $data = array('All');
        foreach ($parameters as $key => $value) {
            $data[] = $value[0];
        }

        $result['result'] = true;
        $result['data'] = $data;

        return json_encode($result);
    }

}
