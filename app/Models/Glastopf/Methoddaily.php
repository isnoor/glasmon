<?php 
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Models\Glastopf;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Methoddaily extends Eloquent {

     protected $connection = 'mongodb';
     protected $collection = 'method_daily';
     protected $dates = ['date'];

     /*public function getTimestamp(){
     		$dateUTC = $this->asDateTime($this->timestamp);
     		return $dateUTC->toDateTimeString();
     		// return $dateUTC->date;
     }*/

}


?>