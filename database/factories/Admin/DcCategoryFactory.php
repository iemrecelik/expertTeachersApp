<?php

namespace Database\Factories\Admin;

use App\Models\Admin\DcCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DcCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'dc_cat_id' => DcCategory::inRandomOrder()->first()->id,
            'dc_cat_name' => $this->faker->name(),
        ];
    }
}
