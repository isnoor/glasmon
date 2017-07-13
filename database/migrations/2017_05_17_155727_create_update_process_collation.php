<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateProcessCollation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     protected $connection = 'mongodb';
    public function up()
    {
        Schema::connection($this->connection)
        ->table('update_process',function (Blueprint $collection)  {
            $collection->index('hpfeed_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::connection($this->connection)
        ->table('update_process', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
