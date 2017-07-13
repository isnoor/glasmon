<?php

use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterCollation extends Migration
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
        ->table('parameter',function (Blueprint $collection)  {
            $collection->index('parameter');
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
        ->table('parameter', function (Blueprint $collection) 
        {
            $collection->drop();
        });
    }
}
