<?php 
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Carbon\Carbon;
use DateTime;
use MongoDate;

class ModelsGeneral extends Eloquent {

     protected $connection = 'mongodb';
     protected $collection = 'migrations';

     public static function newCarbon($timestamp, $isIsoDate=false){
        if($isIsoDate){
            return Carbon::parse($timestamp);
        }else{
            return Carbon::createFromFormat('Y-m-d H:i:s', $timestamp);
        }
    }

    public static function fromDateTimeISODate($timestamp, $isIsoDate=false){
        return  new MongoDate(strtotime($timestamp));
    }

    public static function carbonToUTC($timestamp){        
    	return  new MongoDate(strtotime($timestamp));
    }

}


?>

