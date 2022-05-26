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
        return [
            'sensor_id' => 1,
            'row_count' =>rand(20,30),
            'value' => rand(-10,100),
            'calibration_type' => rand(1,2) 
        ];
    }
}
