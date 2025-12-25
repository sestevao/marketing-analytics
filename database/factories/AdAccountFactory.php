<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdAccount>
 */
class AdAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platform = $this->faker->randomElement(['google', 'facebook', 'linkedin', 'other']);
        
        return [
            'user_id' => User::factory(),
            'name' => ucfirst($platform) . ' Account ' . $this->faker->numerify('###'),
            'platform' => $platform,
            'account_id' => $this->faker->uuid(),
            'access_token' => $this->faker->sha256(),
            'refresh_token' => $this->faker->sha256(),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
