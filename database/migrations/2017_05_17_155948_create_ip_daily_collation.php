<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpDailyCollation extends Migration
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
        ->table('ip_daily',function (Blueprint $collection)  {
            $collection->index('date');
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
        ->table('ip_daily', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
