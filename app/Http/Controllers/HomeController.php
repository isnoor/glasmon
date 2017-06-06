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
use App\Models\Glastopf\Patterndaily;
use App\Models\Glastopf\Parameter;

use App\Models\ModelsGeneral;
use DB;
use DateTime;
use Illuminate\Support\Facades\Input;

class HomeController extends Controller
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
        $countAttack = Event::count();
        $countPattern = Patterndaily::distinct("pattern")->get();
        $countParameter = Parameter::count();
        $countGlastopf = Hpfeed::where("channel","glastopf.events")->distinct('ident')->get();
        $countAttackMonth = Event::where('timestamp', '>=', new DateTime('first day of this month'))->get();
        $eventLast = Event::orderBy('timestamp','desc')->first();
        $count = array("result"=>true,
                        "data"=>array("attack"=>$countAttack, 
                            "pattern"=>count($countPattern),
                            "parameter"=>$countParameter,
                            "glastopf_sensor"=>count($countGlastopf),
                            "attack_last_month"=>count($countAttackMonth),
                            "event_last"=>$eventLast));
        $count = json_encode($count);        
        return $count ;
    }

    public function barChart($period){
      $data= array("labels"=>array(), "data"=>array());
      if($period=="daily"){
         $event = Event::raw()->aggregate([
            ['$match'  => ['timestamp'=>['$gte'=> ModelsGeneral::fromDateTimeISODate($_POST['startDate']), '$lte'=>ModelsGeneral::fromDateTimeISODate($_POST['endDate'])]]], 
            [ '$group' => [
               "_id" => [
                  'day'  => ['$dayOfMonth' => '$timestamp'],
                  'month'  => ['$month' => '$timestamp'],
                  'year'   => ['$year' => '$timestamp']
               ],
               "count" => [ '$sum' => 1 ]
           ]],
           [  '$sort' =>[ '_id.year'=>1, '_id.month'=>1, '_id.day'=>1] ]
        ]);

        foreach ($event['result'] as $key => $value) {
            $data['labels'][] =  $value['_id']['day'].'/'.$this->parsingMonthName($value['_id']['month']).'/'.$value['_id']['year'];
            $data['data'][]= $value['count'];
        }
      }elseif ($period=="weekly") {
         $event = Event::raw()->aggregate([
            ['$match'  => ['timestamp'=>['$gte'=> ModelsGeneral::fromDateTimeISODate($_POST['startDate']), '$lte'=>ModelsGeneral::fromDateTimeISODate($_POST['endDate'])]]], 
            [ '$group' => [
               "_id" => [
                  'week'=>[ '$week'=> '$timestamp' ],
                  'year'   => ['$year' => '$timestamp']
               ],
               "count" => [ '$sum' => 1 ]
           ]],
           [  '$sort' =>[ '_id.year'=>1, '_id.week'=>1] ]
        ]);

        foreach ($event['result'] as $key => $value) {
            $data['labels'][] =  'week '.$value['_id']['week'].'-'.$value['_id']['year'];
            $data['data'][]= $value->count;
         }
      }else{
        $event = Event::raw()->aggregate([
            ['$match'  => ['timestamp'=>['$gte'=> ModelsGeneral::fromDateTimeISODate($_POST['startDate']), '$lte'=>ModelsGeneral::fromDateTimeISODate($_POST['endDate'])]]], 
            [ '$group' => [
               "_id" => [
                   /*"year" => [ '$substr' => [ '$timestamp', 0, 4 ] ],*/
                   /*"month" => [ '$substr' => [ '$timestamp', 5, 2 ] ]*/
                    /*'week'=>[ '$week'=> '$timestamp' ],*/
                   'month'  => ['$month' => '$timestamp'],
                    'year'   => ['$year' => '$timestamp']
               ],
               "count" => [ '$sum' => 1 ]
           ]],
           [  '$sort' =>[ '_id.year'=>1, '_id.month'=>1] ]
        ]);

        foreach ($event['result'] as $key => $value) {
            $data['labels'][] =  $this->parsingMonthName($value['_id']['month']).'-'.$value['_id']['year'];
            $data['data'][]= $value->count;
        } 
      }
        
        $count = json_encode(array("result"=>true,
                        /*"data"=>$count));*/
                        "data"=>$data));
        return $count;
    }

    private function parsingMonthName($monthInt){
        switch ($monthInt) {
            case '01':
                $monthString = 'January';
                break;
            case '02':
                $monthString = 'February';
                break;
            case '03':
                $monthString = 'March';
                break;
            case '04':
                $monthString = 'April';
                break;
            case '05':
                $monthString = 'May';
                break;
            case '06':
                $monthString = 'June';
                break;
            case '07':
                $monthString = 'July';
                break;
            case '08':
                $monthString = 'August';
                break;
            case '09':
                $monthString = 'September';
                break;
            case '10':
                $monthString = 'October';
                break;
            case '11':
                $monthString = 'November';
                break;
            case '12':
                $monthString = 'December';
                break;
            
            default:
                $monthString ='None';
                break;
        }
        return $monthString;
    }

   public function lineChart(){
      $endDate =  ModelsGeneral::newCarbon($_POST['timestamp'], true);
      $endDate->setTimezone('UTC');
      $event = Event::raw()->aggregate([
            ['$match'  => ['timestamp'=>[ '$lte'=>ModelsGeneral::carbonToUTC($endDate), '$gte'=> ModelsGeneral::carbonToUTC($endDate->subMinutes(60))]]], 
            [ '$group' => [
               "_id" => [
                  'day'  => ['$dayOfMonth' => '$timestamp'],
                  'month'  => ['$month' => '$timestamp'],
                  'year'   => ['$year' => '$timestamp'],
                  'hour'   => [ '$hour'=> '$timestamp' ],
                  'minutes'=> [ '$minute'=> '$timestamp' ]
               ],
               "count" => [ '$sum' => 1 ]
           ]],
           [  '$sort' =>[ '_id.hour'=>1, '_id.minutes'=>1] ]
        ]);

      $eventDetect = array();
      foreach ($event['result'] as $key => $value) {
        $minutes = $value['_id']['minutes']< 10 ? '0'.$value['_id']['minutes']: $value['_id']['minutes'];
        $month = $value['_id']['month']< 10 ? '0'.$value['_id']['month']: $value['_id']['month'];
         $indexArray = ModelsGeneral::newCarbon($value['_id']['year'].'-'.$month.'-'.$value['_id']['day'].' '.$value['_id']['hour'].':'.$minutes.':00');
         $eventDetect[$indexArray->format('Y-m-d-H-i')] =$value->count;
      }

      for ($i=0; $i < 59; $i++) { 
         $endDate = $endDate->addMinutes(1);

         $data['labels'][] =  $endDate->toW3cString();
         if(isset($eventDetect[$endDate->format('Y-m-d-H-i')])){
            $data['data'][]= $eventDetect[$endDate->format('Y-m-d-H-i')];
         }else{
            $data['data'][]=0;
         }
      }

      $data = json_encode(array("result"=>true,
                  "data"=>$data));
      return $data;
   }

    public function polling(){
        
        $pollingUpdate = new PollingUpdate();
        $count = $pollingUpdate->doPolling();

        $count = json_encode($count);
        return $count;
    }

    public function event(){
      $limit = Input::has('length')? Input::get('length'):15;
      $offset = Input::has('start')? Input::get('start'):0;
      $event = Event::orderBy('timestamp','asc')->take((int)$limit)->skip((int)$offset)->get()->toArray();
      $result['draw']= Input::has('draw')?Input::get("draw"):1;
      $result["recordsFiltered"]= $result["recordsTotal"]= Event::count();
      $data = array();
      $no = $offset;
      foreach ($event as $key => $value) {
         $no++;
         $source = isset($value['source']['ip'])?$value['source']['ip']:'';
         $destination = isset($value['destination'])? $value['destination'] :'';
         $data[]= array(
            $no,
            $value['timestamp'],
            $destination,
            $source,
            $value['method'],
            implode("<br /> ", $value['parameter']) ,
            implode(", ", $value['pattern']),
            $value["_id"] 
            );
      }
      $result['data'] = $data;
      $result = json_encode($result);

      return $result;
   }

}

