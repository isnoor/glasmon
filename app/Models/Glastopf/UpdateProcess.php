<?php 
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Models\Glastopf;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UpdateProcess extends Eloquent {

     protected $connection = 'mongodb';
     protected $collection = 'update_process';
     protected $dates = ['hpfeed_timestamp','server_timestamp'];

     public function getHpfeedTimestamp(){
     		$dateUTC = $this->asDateTime($this->hpfeed_timestamp);
     		return $dateUTC->toDateTimeString();
     		// return $dateUTC->date;
     }

}


?>