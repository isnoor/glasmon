<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcessCollation extends Migration
{
    /**
         * The name of the database connection to use.
         *
         * @var string
         */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::connection($this->connection)
        ->table('update_process', function (Blueprint $collection) 
        {
            $collection->index('hpfeed_date');
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