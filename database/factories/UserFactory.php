<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'nickname' => fake()->optional()->firstName(),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'matno' => fake()->unique()->bothify('NABAMS####'),
            'phone' => fake()->phoneNumber(),
            'whatsapp_number' => fake()->optional()->phoneNumber(),
            'home_address' => fake()->optional()->address(),
            'department' => 'Business Administration & Management',
            'academic_level' => fake()->randomElement(['ND1', 'ND2', 'ND3', 'HND1', 'HND2', 'HND3', 'GRADUATE']),
            'level_id' => fake()->numberBetween(1, 5),
            'member_type' => fake()->randomElement(['Regular', 'Alumni', 'Part-time']),
            'is_active' => 'Yes',
            'is_ban' => 'No',
            'fee_paid' => fake()->randomElement(['Yes', 'No']),
            'role' => 'Member',
            'user_role_id' => 2,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
