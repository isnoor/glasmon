<?php 
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Models\Mnemosyne;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Hpfeed extends Eloquent {

     protected $connection = 'mongodb1';
     protected $collection = 'hpfeed';
     protected $dates = ['timestamp'];
     public function getTimestamp(){
     		$dateUTC = $this->asDateTime($this->timestamp);
     		return $dateUTC->toDateTimeString();
     		// return $dateUTC->date;
     }
}


?>