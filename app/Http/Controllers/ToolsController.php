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

class ToolsController extends Controller
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
            $userAgentOrig = Event::where('source.ua_browser',$searchQuery)->take((int)$limit)->skip((int)$offset)->get()->toArray();
            $result["recordsFiltered"]= $result["recordsTotal"] = Event::where('source.ua_browser',$searchQuery)->count();
        }else{
            $userAgentOrig = Event::take((int)$limit)->skip((int)$offset)->get()->toArray();
            $result["recordsFiltered"]= $result["recordsTotal"] = Event::count();
        }
        

        $no = $offset;
        foreach ($userAgentOrig as $key => $value) {
            $no++;
            $ua_browser = isset($value['source']['ua_browser'])?$value['source']['ua_browser']:'';
            $ua_orig = isset($value['source']['ua_orig'])?$value['source']['ua_orig']:'';
            $data[] = array($no, $ua_browser, $ua_orig, $value['_id']);
        }
        $result['data'] = $data;
        $result = json_encode($result);

        return $result;
    }

    public function uaFilter(){
        $tools = Event::distinct('source.ua_browser')->get()->toArray();
        $data = array('All');
        foreach ($tools as $key => $value) {
            $data[] = $value[0];
        }

        $result['result'] = true;
        $result['data'] = $data;

        return json_encode($result);
    }

    public function taksonomiList(){
        $limit = Input::has('length')? Input::get('length'):15;
        $offset = Input::has('start')? Input::get('start'):0;
       
        $result['draw']= Input::has('draw')?Input::get("draw"):1;

        $data = array();
        
        $tools = Event::raw()->aggregate([
                    ['$unwind' => '$source' ],
                    ['$group' => ['_id'=> [ 
                                        'tool' => '$source.ua_browser'
                                        ],
                                "count" => [ '$sum' => 1 ]
                                    ]
                    ],
                    ['$sort'=> ['count'=>-1]],
                    ['$skip' => (int)$offset],
                    ['$limit'=> (int)$limit]
                ]);
        $countTools = Event::count();
        $countFilter = Event::distinct('source.ua_browser')->get();
        $result["recordsFiltered"]= $result["recordsTotal"] = count($countFilter);

        $no = $offset;
        foreach ($tools['result'] as $key => $value) {
            $no++;
            $percent = $value['count'] / $countTools * 100;
            $tool = isset($value['_id']['tool'])? $value['_id']['tool']:'unknown';
            $data[] = array($no, $tool, $value['count'], number_format($percent, 2, '.', ' ')." %");
        }
        $result['data'] = $data;
        $result = json_encode($result);

        return $result;
    }

    public function pieChart(){
        $tools = Event::raw()->aggregate([
                    ['$unwind' => '$source' ],
                    ['$group' => ['_id'=> [ 
                                        'tool' => '$source.ua_browser'
                                        ],
                                "count" => [ '$sum' => 1 ]
                                    ]
                    ],
                    ['$sort'=> ['count'=>-1]],
                    ['$limit'=> 20]
                ]);
        $colorDoc =array('rgb(211, 55, 36)','rgb(0, 141, 76)', 'rgb(53, 124, 165)','rgb(219, 139, 11)', 'rgb(85, 82, 153)','rgb(57, 204, 204)' );
       
        $i=0;
        $datasets = array('data'=>array(),'backgroundColor'=>array());
        $labels = array();
        foreach ($tools['result'] as $key => $value) {
            $labels[] = isset($value['_id']['tool'])? $value['_id']['tool']:'unknown';
            $datasets['data'][]=  $value['count'];
            if($i<6){
                $datasets['backgroundColor'][]=  $colorDoc[$i];    
            }else{
                $generateColor = true;
                while ($generateColor) {
                    $color = 'rgb('.rand(0,255).','.rand(0,255).','.rand(0,255).')';
                    if(!in_array($color, $colorDoc)){
                        $datasets['backgroundColor'][]= $color;
                        $generateColor = false;
                    }
                }
            }
            $i++;
        }
        $result['datasets'] = $datasets;
        $result['labels'] = $labels;
        $result = json_encode($result);

        return $result;
    }

}
