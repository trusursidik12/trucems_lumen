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

$router->get('/', 'DashboardController@index');
$router->get('/quality-standards', 'DashboardController@qualityStandard');
$router->group(['prefix' => 'calibration'], function() use ($router){
    $router->get('/manual', 'CalibrationController@manual');
    $router->get('/auto', 'CalibrationController@auto');
    $router->get('/logs', 'CalibrationController@logs');
    $router->get('/{mode}/{type}/process', 'CalibrationController@processCal');

});
/**
 * API
 */
$router->group(['prefix' => 'api'], function() use ($router){
    $router->get('/runtime','API\RuntimeController@index');
    $router->patch('/runtime','API\RuntimeController@store');
    /**
     * Set Calibration
     */
    $router->patch('/set-calibration/{mode}/{type}','API\SetCalibrationController@setCalibration');
    $router->get('/calibration/check-remaining/{mode}/{type}','API\SetCalibrationController@checkRemaining');
    $router->get('/calibration/check-retry/{mode}/{type}','API\SetCalibrationController@retryCalibration');
    $router->get('/calibration/update-calibration/{mode}/{type}','API\SetCalibrationController@updateStatusCalibration');
    $router->patch('/calibration/update-time-calibration/{mode}/{type}','API\SetCalibrationController@updateTimeCalibration');
    /**
     * Sensor Value Logs
     */
    $router->patch('/sensor-value/{sensorId}', 'API\ValueLogsController@update');
    $router->get('/sensor-value-logs', 'API\ValueLogsController@index');

    /**
     * Calibration Logs
     */
    $router->get('/calibration-logs/get-last', 'API\CalibrationLogsController@getLast');
    $router->post('/calibration-logs', 'API\CalibrationLogsController@store');
    $router->get('/calibration-logs', 'API\CalibrationLogsController@index');
    $router->delete('/calibration-logs', 'API\CalibrationLogsController@destroy');
    /**
     * Calibration AVG Logs
     */
    $router->post('/calibration-avg-logs', 'API\CalibrationAvgLogsController@store');
    $router->get('/calibration-avg-logs', 'API\CalibrationAvgLogsController@index');
    $router->get('/calibration-avg-logs/paginate', 'API\CalibrationAvgLogsController@logs');
    $router->get('/calibration-avg-logs/export', 'API\CalibrationAvgLogsController@export');
    /**
     * Configurations
     */
    $router->get('/configurations','API\ConfigurationController@index');
    $router->patch('/configurations','API\ConfigurationController@update');

    
});
