<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_name' => $this->faker->words(2, true),           // e.g., "Oil Change"
            'description' => $this->faker->sentence(),                 // e.g., "Complete oil replacement for your car"
            'price' => $this->faker->randomFloat(2, 50, 5000),        // random price between 50 and 5000
            'duration_minutes' => $this->faker->numberBetween(30, 180), // duration in minutes
            'is_active' => $this->faker->boolean(80),                 // 80% chance to be active
            'image' => $this->faker->imageUrl(640, 480, 'transport', true), // placeholder image
        ];
    }
}
