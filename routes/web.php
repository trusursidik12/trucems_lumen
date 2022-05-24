<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\ExampleController;

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

$router->get('/', function () use ($router) {
    
});
/**
 * API
 */
$router->group(['prefix' => '/api'], function() use ($router){
    /**
     * Sensor Value Logs
     */
    $router->patch('/sensor-value/{sensorId}', 'API\ValueLogsController@update');
    $router->get('/sensor-value-logs', 'API\ValueLogsController@index');

    /**
     * Calibration Logs
     */
    $router->post('/calibration-logs', 'API\CalibrationLogsController@store');
    $router->get('/calibration-logs', 'API\CalibrationLogsController@index');
    /**
     * Calibration Logs
     */
    $router->post('/calibration-avg-logs', 'API\CalibrationLogsController@store');
    $router->get('/calibration-avg-logs', 'API\CalibrationLogsController@index');
    /**
     * Configurations
     */
    $router->get('/configurations','API\ConfigurationController@index');
    $router->patch('/configurations','API\ConfigurationController@update');

    
});
