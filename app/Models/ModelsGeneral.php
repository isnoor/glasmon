<?php 
/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class ModelsGeneral extends Eloquent {

     protected $connection = 'mongodb';
     protected $collection = 'migrations';

}


?>