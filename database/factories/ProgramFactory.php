<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Program::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $faker->name,
            'code' => $faker->stateAbbr,
            'email' => $faker->email,
            'keyword' => $faker->cityPrefix,
            'category_id' => $faker->name,
            'begin_at' => $faker->dateTime,
            'end_at' => null,
            'description' => $faker->text,
            // 'pictures' => $faker->url,
            'has_content' => false,
        ];
    }
}
