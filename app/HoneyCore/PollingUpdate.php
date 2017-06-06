<?php
/*=================================================
* Glasmon 1.0 (MHN addons)
* modul sync MHN - HIHAT
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\HoneyCore;

set_time_limit(0);
use App\Models\Mnemosyne\Hpfeed;
use App\Models\Glastopf\UpdateProcess;
use App\Models\Glastopf\Event;
use App\Models\Glastopf\Patterndaily;
use App\Models\Glastopf\Ipdaily;
use App\Models\Glastopf\Parameter;
use App\Models\ModelsGeneral;
use DB;
use DateTime;
use App\HoneyCore\HIHATCore\attacklist;
use App\HoneyCore\HIHATCore\browserCheck;
use App\HoneyCore\HIHATCore\filters;

class PollingUpdate /*extends Controller*/
{
    /**
     * Create class for polling update db
     *
     * @return void
     */
   public function __construct()
   {
        //
   }

   public function doPolling(){
      $hpfeed = $this->getUnDataAttact();
/*echo '<pre>';print_r($hpfeed[0]->timestamp->format("Y-m-d H:i:s"));echo '</pre>';die('sublime_cek');*/
      $count = $this->analysisData($hpfeed);
      
      return $count;
   }
    /*attack on mhn hpfeed format*/
   public function analysisData($attack){
      if(!$attack){
         return 0;
      }

      if (count($attack)>0) {
         $http_request_tag = array("Accept:", "Accept-Charset:", "Accept-Encoding:",
            "Accept-Language:",
            "Authorization:",       
            "Expect:",    
            "From:",                 
            "Host:",                   
            "If-Match:",
            "If-Modified-Since:",
            "If-None-Match:",       
            "If-Range:",          
            "If-Unmodified-Since:",
            "Max-Forwards:",    
            "Proxy-Authorization:",
            "Range:",    
            "Referer:",                  
            "TE:",                
            "User-Agent:",
            "Allow:",            
            "Upgrade-Insecure-Requests:",
            "Content-Encoding:",
            "Content-Language:",       
            "Content-Length:",       
            "Content-Location:",         
            "Content-MD5:",       
            "Content-Range:",            
            "Content-Type:",          
            "Expires:",           
            "Last-Modified:",
            "extension-header:",
            "Cache-Control:",
            "Connection:",
            "Origin:",
            "Pragma:");
         $browserCheck = new browserCheck(); 
         $attacksCategory = new attacklist();
         
         $countDetect = 0 ;
         foreach ($attack as $key => $row) {
            // init variables for attack-detection  ( as new table starts )
            $payload = $row->payload;
            $attackRow = new Event();
            $attackRowPattern = $attackRowSource =  $attackRowParameter = array();

            // initialize variables for storage of $_SERVER-data
            $attackRow->hpfeed_id = $row->_id;
            /*$attackRow->referrer'] = $payload['request_url'];*/
            $attackRowPattern[] = $payload['pattern'] ;
            $attackRow->request_orig = $payload['request_raw'];
            $attackRow->timestamp = $row->timestamp/*->format('Y-m-d H:i:s')*/;
            $attackRowSource['ip'] = $payload['source'][0];
            $attackRowSource['port'] = $payload['source'][1];
            $attackRowSource['ua_orig'] = "unknown";
            $attackRow->sensor_id = $payload["sensorid"];
            $attackRow->ident = $row->ident;

            $request_raw = $payload['request_raw'];
            
            $request_raw = explode("\r\n", $request_raw);
            
            $attackRowMethod = explode(' ',$request_raw[0], 2);
            $attackRow->method = $attackRowMethod[0];

            $attackRowHttp_v = explode(' ',$request_raw[0]);
            $attackRowHttp_v = $attackRowHttp_v[count($attackRowHttp_v)-1];
            $attackRowHttp_v = explode('HTTP/',$attackRowHttp_v);
            $attackRow->http_v = $attackRowHttp_v[1];

            $attackRowParameter[] = $payload['request_url'];
            $i=0;
            foreach ($request_raw as $request_key => $request_value) {
               if($i==0){$i++;continue;}
               if (strpos($request_value, 'User-Agent:') !== false) {
                  $attackRowSource['ua_orig'] = explode("User-Agent:", $request_value);
                  $attackRowSource['ua_orig'] = $attackRowSource['ua_orig'][1];
                  $attackRowSource['ua_browser'] =$browserCheck->browserCheckCore($attackRowSource['ua_orig']);
               }elseif(strpos($request_value, 'Host:') !== false){
                  $attackRow["destination"] = explode('Host:', $request_value);
                  $attackRow["destination"] = $attackRow->destination[1];
               }else{
                  $tag_cek =false;
                  foreach ($http_request_tag as $key_tag => $tag) {
                     if((strpos($request_value, $tag) !== false) || (strpos($request_value, ":") !== false)){
                        $tag_cek =true; break;
                     }
                  }
                  if(!$tag_cek&& $request_value!=""){
                     $attackRowParameter[] = $request_value;   
                  }
               }
            }
            if($attackRowParameter[0]=="/"){
               $attackRowPattern[0]="scanning";
            }else if($attackRowPattern[0]=="unknown")
            {
               $parameter = $attackRowParameter;
               $bufParameter = array();
               for($i=0;$i<count($parameter); $i++ ){
                  $tempParameter = explode("?", $parameter[$i],2);
                  $bufParameter = array_merge($bufParameter,$tempParameter);
               }
               $parameter = $bufParameter;
               unset($bufParameter);
               unset($tempParameter);

               foreach($parameter as $params_key => $params_value) {
                  if ( $params_value != "" ) {
                     $oneLine = explode( "=", $params_value , 2);    // split into variable and value   
                                                 
                     // filter $_SERVER array
                     $leftSide =  isset($oneLine[0]) ? $oneLine[0] : "";
                     $rightSide = isset($oneLine[1]) ? $oneLine[1] : "";

                     $tmpAttack = filters::attackChecking( $leftSide, $rightSide, $attacksCategory, false );           
                     if ( $tmpAttack !== -1) {
                        foreach ($tmpAttack as $tmpAttackKey => $tmpAttackValue) {
                           if($attackRowPattern[0]=="unknown"){
                              $attackRowPattern[0]=$tmpAttackValue;
                           }else if (!in_array($tmpAttackValue, $attackRowPattern)) {
                              $attackRowPattern[]=$tmpAttackValue;
                           }
                        }                                                
                     }
                  } 
               }   /*end of foreach*/
            }
            

            /*insert into event*/
            $attackRow->pattern = $attackRowPattern;
            $attackRow->source  = $attackRowSource;
            $attackRow->parameter =  $attackRowParameter;
            $attackRow->save();
            /*Event::insert($attackRow);*/
            $countDetect++;
            $this->addCounterPatternDaily($attackRowPattern, $row->timestamp);
            $this->addCounterIpDaily($attackRowSource['ip'], isset($attackRow->destination)?$attackRow->destination:"", $row->timestamp);
            $this->addCounterParameter($attackRow->parameter, $attackRowPattern, $row->timestamp);
            /*insert into pattern_daily*/

         } // end: foreach master
         
         $lastTime = $attack[$countDetect-1];
         $lastTime = $lastTime->timestamp->addSecond();
         $lastTime = $lastTime->format('Y-m-d H:i:s') ;
         $updateProcess = array("hpfeed_timestamp"=>$lastTime, "server_timestamp"=>date("Y-m-d H:i:s"));

         UpdateProcess::insert($updateProcess);
         return $countDetect;
      } else 
         return 0;
   }

   public function addCounterPatternDaily($pattern, $timestamp){
      foreach ($pattern as $key => $value) {
         $pattern_daily = Patterndaily::select('daily')->where("date","=",$timestamp->format('Y-m-d'))->where("pattern","=",$value)->first();
         $hourly = $timestamp->format('H');

         if(count($pattern_daily) > 0){
            $daily = $pattern_daily->daily;
            if (array_key_exists($hourly, $daily)){
               $daily[$hourly] = $daily[$hourly] +1;
            }else{
               $daily[$hourly] = 1;
            }

            $pattern_daily->daily = $daily;
            $result = $pattern_daily->save();
         }else{
            $pattern_daily = array("date"=>$timestamp->format('Y-m-d') ,
                                    "pattern"=>$value,
                                    "daily"=>array($hourly=>1));
            $result = Patterndaily::insert($pattern_daily);
         }

         return $result;
      }
   }

   public function addCounterIpDaily($source, $destination, $timestamp){
      $ip_daily = Ipdaily::select('daily')->where("date","=",$timestamp->format('Y-m-d'))->where("source","=",$source)->where("destination","=",$destination)->first();
      $hourly = $timestamp->format('H');

      $source_country = "unknown";
      if(isset($source) && $source !==""){
         if (!filter_var($source, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false) {
            $source_country = app()->geoip->getLocation($source)->country->names['en'];
         }else{
            $source_country = "internal network";
         }
         
      }
      if(count($ip_daily) > 0){
         $daily = $ip_daily->daily;
         if (array_key_exists($hourly, $daily)){
               $daily[$hourly] = $daily[$hourly] +1;
         }else{
               $daily[$hourly] = 1;
         }

         $ip_daily->daily = $daily;
         $result = $ip_daily->save();
      }else{
         $ip_daily = array("date"=>$timestamp->format('Y-m-d') ,
                           "source"=>$source,
                           "source_country"=>$source_country,
                           "destination" =>$destination,
                           "daily"=>array($hourly=>1));
         $result = Ipdaily::insert($ip_daily);
      }
      return $result;
   }

   public function addCounterParameter($parameter, $pattern, $timestamp){
      if(count($parameter) == 1){
         $parameter_db = Parameter::select('event')->where("parameter","=",$parameter[0])->where("pattern","=",$pattern)->first();   
         if(count($parameter_db) > 0){
            $parameter_event = $parameter_db->event;
            $is_exist = false;
            $event_change = array();
            $i=0;
            foreach ($parameter_event as $key => $value) {
               if(in_array($timestamp->format('Y-m-d'), $value)){
                  $parameter_event[$i]=array("date"=>$value["date"],"count"=>($value["count"]+1));
                  $is_exist = true;
               }
               $i++;
            }
            if(!$is_exist){
               $parameter_event[]= array("date"=>$timestamp->format('Y-m-d'),"count"=>1);
            }

            $parameter_db->event = $parameter_event;
            $result = $parameter_db->save();
         }else{
            $parameter_db = array("parameter"=>$parameter[0],
                                    "pattern" => $pattern,
                                    "event"  =>array(array("date"=>$timestamp->format('Y-m-d'),"count"=>1) ));
            $result = Parameter::insert($parameter_db);
         }
      }else{
         $param_index =0;
         foreach ($parameter as $param_key => $param_value) {
            $parameter_db = Parameter::select('event')->where("parameter","=",$param_value)->first();   
            if(count($parameter_db) > 0){
               $parameter_event = $parameter_db->event;
               $is_exist = false;
               $event_change = array();
               $i=0;
               foreach ($parameter_event as $key => $value) {
                  if(in_array($timestamp->format('Y-m-d'), $value)){
                     $parameter_event[$i]=array("date"=>$value["date"],"count"=>($value["count"]+1));
                     $is_exist = true;
                  }
                  $i++;
               }
               if(!$is_exist){
                  $parameter_event[]= array("date"=>$timestamp->format('Y-m-d'),"count"=>1);
               }

               $parameter_db->event = $parameter_event;
               $result = $parameter_db->save();
            }else{
               if($param_index == 0){
                  $parameter_db = array("parameter"=>$parameter[0],
                                       "pattern" => $pattern[0],
                                       "event"  =>array(array("date"=>$timestamp->format('Y-m-d'),"count"=>1) ));
               }else{
                  if(count($pattern)==1){
                     $pattern_temp = $pattern;
                  }else{
                     $pattern_temp = array("unknown");
                     $oneLine = explode( "=", $parameter[1] , 2);    // split into variable and value   
                                                 
                     // filter $_SERVER array
                     $leftSide =  isset($oneLine[0]) ? $oneLine[0] : "";
                     $rightSide = isset($oneLine[1]) ? $oneLine[1] : "";

                     $attacksCategory = new attacklist();
                     $pattern_result = filters::attackChecking( $leftSide, $rightSide, $attacksCategory, false );
                     if ( $$pattern_result !== -1) {
                        $pattern_temp = $pattern_result;
                     }
                  }
                  
                  $parameter_db = array("parameter"=>$parameter[1],
                                       "pattern" => $pattern_temp,
                                       "event"  =>array(array("date"=>$timestamp->format('Y-m-d'),"count"=>1) ));
               }
               
               $result = Parameter::insert($parameter_db);
            }
            $param_index++;
         }
      }
      return $result;
   }
      

   public function getUnDataAttact(){
      $updateProcess = UpdateProcess::orderBy('hpfeed_timestamp','desc')->first();
        
      if($updateProcess){
         $hpfeed = Hpfeed::where('timestamp', '>', new DateTime($updateProcess->getHpfeedTimestamp()))->where('channel','glastopf.events')->get();  
      }else{
            $hpfeed = Hpfeed::where('channel','glastopf.events')->get();
      }
      return $hpfeed;
   }

}
