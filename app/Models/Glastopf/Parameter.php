<?php 
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Models\Glastopf;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Parameter extends Eloquent {

     protected $connection = 'mongodb';
     protected $collection = 'parameter';
     /*protected $dates = ['timestamp'];*/

     /*public function getTimestamp(){
     		$dateUTC = $this->asDateTime($this->timestamp);
     		return $dateUTC->toDateTimeString();
     		// return $dateUTC->date;
     }*/

}


?>