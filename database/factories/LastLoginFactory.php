<?php

namespace Database\Factories;

use App\Models\LastLogin;
use Illuminate\Database\Eloquent\Factories\Factory;

class LastLoginFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LastLogin::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "ip_address" => $this->faker->ipv4,
        ];
    }
}
