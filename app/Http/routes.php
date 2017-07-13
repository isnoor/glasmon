<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/*=================================================
* Glasmon 1.0 (MHN addons)
* Created  May 2017
* isnoor.laksana@mail.ugm.ac.id
*==================================================*/
$app->get('/', function () use ($app) {
    return view('layout');
});

$app->get('/polling',"PollingController@polling");

$app->post('/polling/new',"PollingController@pollingCount");

$app->post('/api/home',"HomeController@index");

$app->post( '/api/home/line_chart' , "HomeController@lineChart");

$app->post( '/api/home/bar_chart/{period}' , "HomeController@barChart");

$app->post( '/api/home/event' , "HomeController@event");

$app->post( '/api/sensor/list' , "SensorController@index");

$app->post( '/api/method/pie' , "MethodController@index");

$app->post( '/api/tools/pie' , "ToolsController@pieChart");

$app->post( '/api/tools/list' , "ToolsController@index");

$app->post( '/api/tools/taksonomi/list' , "ToolsController@taksonomiList");

$app->post( 'api/tools/filter/list' , "ToolsController@uaFilter");

$app->post( '/api/parameter/list' , "ParameterController@index");

$app->post( '/api/parameter/filter/list' , "ParameterController@filterList");

$app->post( '/api/pattern/pie' , "PatternController@pieChart");

$app->post( '/api/pattern/list' , "PatternController@index");

$app->post( '/api/ip_address/pie' , "IPAddressController@pieChart");

$app->post( '/api/ip_address/line' , "IPAddressController@lineChart");

$app->post( '/api/country/pie' , "IPAddressController@countryPieChart");

$app->post( '/api/country/list' , "IPAddressController@index");

$app->post( 'api/country/filter/list' , "IPAddressController@countryFilter");

$app->get( '/css/{file}' , function($file) {
    return public_path() . '/css/'.$file;
});

$app->get( '/js/{file}' , function($file) {
    return public_path() . '/js/'.$file;
});
