<?php

namespace Database\Factories;

use App\Models\CalibrationAvgLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CalibrationAvgLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CalibrationAvgLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $type = rand(1,2);
        return [
            'sensor_id' => 1,
            'row_count' =>rand(20,30),
            'value' => rand(-10,100),
            'calibration_type' => $type,
            'cal_gas_ppm' => ($type == 2 ? rand(1,4) : 0),
            'cal_duration' => rand(1,10)
        ];
    }
}
