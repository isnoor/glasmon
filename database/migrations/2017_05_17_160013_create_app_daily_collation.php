<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppDailyCollation extends Migration
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
        ->table('app_daily',function (Blueprint $collection)  {
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
        ->table('app_daily', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
