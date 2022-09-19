<?php

namespace Database\Factories\Admin\OldRegulation;

use Illuminate\Database\Eloquent\Factories\Factory;

class OldSecondColAppFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'ofc_app_tc_num' => $this->faker->numberBetween($min = 10000000000, $max = 99999999999),
            'ofc_app_name' => $this->faker->name(),
            'ofc_app_surname' => $this->faker->name(),
            'ofc_app_min_puan' => $this->faker->randomFloat(1, 20, 30),
            'ofc_app_main_puan' => $this->faker->randomFloat(1, 20, 30)
        ];
    }
}
