<?php
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Http\Controllers;

set_time_limit(0);
use App\Models\Glastopf\Ipdaily;
use Illuminate\Support\Facades\Input;

use App\Models\Glastopf\Event;
use App\Models\Glastopf\Patterndaily;
use App\Models\Glastopf\Parameter;

use App\Models\ModelsGeneral;
use DB;
use DateTime;

class IPAddressController extends Controller
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
            $listData = Ipdaily::raw()->aggregate([
                    ['$match' =>['source_country'=>$searchQuery] ], 
                    ['$unwind' => '$daily' ],
                    ['$group' => ['_id'=> [
                                        '_id'=> '$_id', 
                                        'source' => '$source', 
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
                        '_id'=> [ 'source' => '$_id.source'], 
                        'total' => [ '$sum' =>  '$_id.totalPerRow']
                        ]
                    ],
                    ['$sort'=> ['total'=>-1]],
                    ['$skip' => (int)$offset],
                    ['$limit'=> (int)$limit]
                ]);
            $count = Ipdaily::where('source_country',$searchQuery)->distinct("source")->get();
            $result["recordsFiltered"]= $result["recordsTotal"] = count($count);
        }else{
            $listData = Ipdaily::raw()->aggregate([ 
                    ['$unwind' => '$daily' ],
                    ['$group' => ['_id'=> [
                                        '_id'=> '$_id', 
                                        'source' => '$source', 
                                        'country' => '$source_country',
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
                        '_id'=> [ 'source' => '$_id.source', 'country'=>'$_id.country'], 
                        'total' => [ '$sum' =>  '$_id.totalPerRow'],
                        ]
                    ],
                    ['$sort'=> ['total'=>-1]],
                    ['$skip' => (int)$offset],
                    ['$limit'=> (int)$limit]
                ]);
            $count = Ipdaily::distinct("source")->get();
            $result["recordsFiltered"]= $result["recordsTotal"] =  count($count);
        }
        

        $no = $offset;
        foreach ($listData as $key => $value) {
            $no++;
            if($searchQuery!=="all" && $searchQuery!=="All"){
                $data[] = array("no"=>$no, "country"=>$searchQuery, "ip"=>$value->_id->source, "count"=>$value->total);
            }else{
                $data[] = array("no"=>$no, "country"=>$value->_id->country, "ip"=>$value->_id->source, "count"=>$value->total);
            }
            
        }
        $result['data'] = $data;
        $result = json_encode($result);

        return $result;
    }

    public function countryFilter(){
        $countries = Ipdaily::distinct('source_country')->get()->toArray();
        $data = array('All');
        foreach ($countries as $key => $value) {
            $data[] = $value[0];
        }

        $result['result'] = true;
        $result['data'] = $data;

        return json_encode($result);
    }

    public function countryPieChart(){
        $countries = Ipdaily::raw()->aggregate([
                    ['$unwind' => '$daily' ],
                    ['$group' => ['_id'=> [
                                        '_id'=> '$_id', 
                                        'country' => '$source_country', 
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
                        '_id'=> [ 'country' => '$_id.country'], 
                        'total' => [ '$sum' =>  '$_id.totalPerRow']
                        ]
                    ],
                    ['$sort'=> ['total'=>-1]],
                    ['$limit'=> 10]
                ]);
        $colorDoc =array('rgb(211, 55, 36)','rgb(0, 141, 76)', 'rgb(53, 124, 165)','rgb(219, 139, 11)', 'rgb(85, 82, 153)','rgb(57, 204, 204)' );
       
        $i=0;
        $datasets = array('data'=>array(),'backgroundColor'=>array());
        $labels = array();
        foreach ($countries as $key => $value) {
            $labels[] = $value->_id->country;
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

    public function pieChart(){
        $ipAddress = Ipdaily::raw()->aggregate([
                    ['$unwind' => '$daily' ],
                    ['$group' => ['_id'=> [
                                        '_id'=> '$_id', 
                                        'source' => '$source', 
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
                        '_id'=> [ 'source' => '$_id.source'], 
                        'total' => [ '$sum' =>  '$_id.totalPerRow']
                        ]
                    ],
                    ['$sort'=> ['total'=>-1]],
                    ['$limit'=> 10]
                ]);
        $colorDoc =array('rgb(211, 55, 36)','rgb(0, 141, 76)', 'rgb(53, 124, 165)','rgb(219, 139, 11)', 'rgb(85, 82, 153)','rgb(57, 204, 204)' );
       
        $i=0;
        $datasets = array('data'=>array(),'backgroundColor'=>array());
        $labels = array();
        foreach ($ipAddress as $key => $value) {
            $labels[] = $value->_id->source;
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

    public function lineChart(){
      $ipAddressSearch = Input::has('ip_address')?Input::get("ip_address"):'';
      $ipAddress = Ipdaily::where('source', $ipAddressSearch)->orderBy('date','asc')->get()->toArray();

      $data = array();
      $cache = array();
      foreach ($ipAddress as $key => $row) {
         foreach ($row['daily'] as $hour => $count) {
            $date = explode(' ', $row['date']); 
            if($hour>0){
                if(!isset($cache[$date[0]][$hour-1])){
                    $cache[$date[0]][$hour-1]['count'] =0;
                }    
            }
            
            if(isset($cache[$date[0]][$hour])){
               $cache[$date[0]][$hour]['count'] = $cache[$date[0]][$hour]['count'] + $count;
            }else{
               $cache[$date[0]][$hour]['count'] = $count;
            }

            if($hour<24){
                if(!isset($cache[$date[0]][$hour+1])){
                    $cache[$date[0]][$hour+1]['count'] =0;
                }    
            }
         }
      }

      foreach ($cache as $date => $daily) {
         ksort($daily);
         foreach ($daily as $hour => $value) {
            $data[]= array("x"=>$date.' '.$hour.":00", "y"=>$value['count']);  
         }
      }
      
      $data = json_encode(array("result"=>true,
                  "data"=>$data));
      return $data;  
    }

}
