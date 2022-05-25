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
$router->group(['prefix' => 'calibration'], function () use ($router) {
    $router->get('/manual', 'CalibrationController@manual');
    $router->get('/auto', 'CalibrationController@auto');
    $router->get('/logs', 'CalibrationController@logs');
});
/**
 * API
 */
$router->group(['prefix' => 'api'], function () use ($router) {
    /**
     * Set Calibration
     */
    $router->patch('/set-calibration/manual/{type}','API\SetCalibrationController@setManualCal');
    $router->get('/calibration/check-remaining/{mode}/{type}','API\SetCalibrationController@checkRemaining');
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
    $router->delete('/calibration-logs', 'API\CalibrationLogsController@destroy');
    /**
     * Calibration AVG Logs
     */
    $router->post('/calibration-avg-logs', 'API\CalibrationAvgLogsController@store');
    $router->get('/calibration-avg-logs', 'API\CalibrationAvgLogsController@index');
    $router->get('/calibration-avg-logs/paginate', 'API\CalibrationAvgLogsController@logs');
    /**
     * Configurations
     */
    $router->get('/configurations', 'API\ConfigurationController@index');
    $router->patch('/configurations', 'API\ConfigurationController@update');
});
