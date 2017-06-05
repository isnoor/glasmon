<?php
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Http\Controllers;

set_time_limit(0);
use App\Models\Glastopf\Patterndaily;
use Illuminate\Support\Facades\Input;

class PatternController extends Controller
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
       
        $result['draw']= Input::has('draw')?Input::get("draw"):1;

        $data = array();
        
        $patterns = Patterndaily::raw()->aggregate([
                    ['$unwind' => '$daily' ],
                    ['$group' => ['_id'=> [
                                        '_id'=> '$_id', 
                                        'pattern' => '$pattern', 
                                        'totalPerRow' => ['$sum' =>  [
                                                                    '$daily.01','$daily.02',
                                                                    '$daily.03','$daily.04',
                                                                    '$daily.05','$daily.06',
                                                                    '$daily.07','$daily.08',
                                                                    '$daily.09','$daily.10',
                                                                    '$daily.11','$daily.12',
                                                                    '$daily.13','$daily.14',
                                                                    '$daily.15','$daily.16',
                                                                    '$daily.17','$daily.18',
                                                                    '$daily.19','$daily.20',
                                                                    '$daily.21','$daily.22',
                                                                    '$daily.23','$daily.24','$daily.00'
                                                                    ]
                                                            ]
                                        ]
                                    ]
                    ],
                    ['$unwind' => '$_id' ],
                    ['$group' => [
                        '_id'=> [ 'pattern' => '$_id.pattern'], 
                        'total' => [ '$sum' =>  '$_id.totalPerRow']
                        ]
                    ],
                    ['$sort'=> ['total'=>-1]],
                    ['$skip' => (int)$offset],
                    ['$limit'=> (int)$limit]
                ]);
        $countPattern = Patterndaily::distinct("pattern")->get();
        $result["recordsFiltered"]= $result["recordsTotal"] = count($countPattern);

        $no = $offset;
        foreach ($patterns as $key => $value) {
            $no++;
            $data[] = array($no, $value->_id->pattern, $value->total);
        }
        $result['data'] = $data;
        $result = json_encode($result);

        return $result;
    }

    public function pieChart(){
        $patterns = Patterndaily::raw()->aggregate([
                    ['$unwind' => '$daily' ],
                    ['$group' => ['_id'=> [
                                        '_id'=> '$_id', 
                                        'pattern' => '$pattern', 
                                        'totalPerRow' => ['$sum' =>  [
                                                                    '$daily.01','$daily.02',
                                                                    '$daily.03','$daily.04',
                                                                    '$daily.05','$daily.06',
                                                                    '$daily.07','$daily.08',
                                                                    '$daily.09','$daily.10',
                                                                    '$daily.11','$daily.12',
                                                                    '$daily.13','$daily.14',
                                                                    '$daily.15','$daily.16',
                                                                    '$daily.17','$daily.18',
                                                                    '$daily.19','$daily.20',
                                                                    '$daily.21','$daily.22',
                                                                    '$daily.23','$daily.24','$daily.00'
                                                                    ]
                                                            ]
                                        ]
                                    ]
                    ],
                    ['$unwind' => '$_id' ],
                    ['$group' => [
                        '_id'=> [ 'pattern' => '$_id.pattern'], 
                        'total' => [ '$sum' =>  '$_id.totalPerRow']
                        ]
                    ],
                    ['$sort'=> ['total'=>-1]],
                    ['$limit'=> 15]
                ]);
        $colorDoc =array('rgb(211, 55, 36)','rgb(0, 141, 76)', 'rgb(53, 124, 165)','rgb(219, 139, 11)', 'rgb(85, 82, 153)','rgb(57, 204, 204)' );
       
        $i=0;
        $datasets = array('data'=>array(),'backgroundColor'=>array());
        $labels = array();
        foreach ($patterns as $key => $value) {
            $labels[] = $value->_id->pattern;
            $datasets['data'][]=  $value->total;
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
