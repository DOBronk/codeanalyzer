<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jobitem>
 */
class JobitemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => fake()->filePath(),
            'sha' => fake()->sha256(),
            'status_id' => 0,
            'results' => ''
        ];
    }

    public function processed(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => 1,
                'results' => json_encode(['SRP' => 1, 'DRY' => fake()->words(10,true), 'OCP' => 1]),
            ];
        });
    }


    public function issueCreated(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status_id' => 3,
            ];
        });
    }
}
