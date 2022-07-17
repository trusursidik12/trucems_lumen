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
$router->get('/configurations', 'ConfigurationController@index');
$router->patch('/configurations', 'ConfigurationController@update');

$router->get('/plc-simulation', 'Debug\DebugController@plc');
$router->get('/plc-simulation/data', 'Debug\DebugController@getPLC');

$router->group(['prefix' => 'calibration'], function () use ($router) {
    $router->get('/manual', 'CalibrationController@manual');
    $router->get('/auto', 'CalibrationController@auto');
    $router->get('/logs', 'CalibrationController@logs');
    $router->get('/{mode}/{type}/process', 'CalibrationController@processCal');
});
/**
 * API
 */
$router->group(['prefix' => 'api'], function () use ($router) {
    /**
     * PLC API
     *
     */
    $router->get('/plc', 'API\PlcController@index'); //get data all plc
    $router->patch('/alarm/update', 'API\PlcController@updateAlarm');
    $router->patch('/start-plc', 'API\PlcController@updatePLC');
    $router->patch('/start-cal', 'API\PlcController@updateCal');
    /**
     * Runtime API
     */
    $router->get('/runtime', 'API\RuntimeController@index');
    $router->patch('/runtime', 'API\RuntimeController@store');
    /**
     * Set Relay API
     */
    $router->get('/relay', 'API\RelayController@index');
    $router->patch('/relay', 'API\RelayController@setRelay');
    // Blowback
    $router->get('/blowback', 'API\BlowbackController@checkRemaining');
    $router->patch('/blowback', 'API\BlowbackController@setBlowback');
    $router->patch('/blowback/finish', 'API\BlowbackController@finishBlowback');
    /**
     * Set Calibration
     */
    $router->patch('/set-calibration/{mode}/{type}', 'API\SetCalibrationController@setCalibration');
    $router->get('/calibration/check-remaining/{mode}/{type}', 'API\SetCalibrationController@checkRemaining');
    $router->get('/calibration/check-retry/{mode}/{type}', 'API\SetCalibrationController@retryCalibration');
    $router->get('/calibration/update-calibration/{mode}/{type}', 'API\SetCalibrationController@updateStatusCalibration');
    $router->patch('/calibration/update-time-calibration/{mode}/{type}', 'API\SetCalibrationController@updateTimeCalibration');

    /**
     * Start Calibration
     */
    $router->post('/calibration-start', 'API\SetCalibrationController@calibrationStart');
    $router->post('/calibration-set-value/{type}', 'API\SetCalibrationController@offsetAndGain');
    $router->post('/calibration-last-value', 'API\SetCalibrationController@getLastRecord');
    $router->post('/calibration-stop', 'API\SetCalibrationController@closeCalibration');

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
    $router->get('/configurations', 'API\ConfigurationController@index');
    $router->patch('/configurations', 'API\ConfigurationController@update');
});
