<?php

namespace Modules\Auth\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Auth\Models\User;
use Modules\Base\Enums\Gender;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Auth\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = \Modules\Auth\Models\User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'type'              => User::SERVICE_PROVIDER,
            'phone_number'      => '+' . fake()->unique()->numerify('90##########'),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => 'password',
            'lang'              => fake()->randomElement(LaravelLocalization::getSupportedLanguagesKeys()),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
            'service_id'        => Service::query()->inRandomOrder()->value('id'),
            'city_id'           => City::query()->inRandomOrder()->value('id'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
