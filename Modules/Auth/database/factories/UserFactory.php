<?php

namespace Modules\Auth\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Auth\Enums\UserType;
use Modules\Auth\Models\User;
use Modules\Platform\Models\Service;
use Modules\Zms\Models\City;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'type' => UserType::ServiceProvider,
            'phone_number' => '+'.fake()->unique()->numerify('90##########'),
            'central_phone' => $this->faker->phoneNumber,
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'lang' => fake()->randomElement(LaravelLocalization::getSupportedLanguagesKeys()),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'service_id' => Service::query()->inRandomOrder()->value('id'),
            'city_id' => City::query()->inRandomOrder()->value('id'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            $randomImagePath = asset('modules/admin/metronic/demo/media/avatars/300-'.rand(1, 30).'.jpg');
            $user->addMediaFromUrl($randomImagePath)->toMediaCollection(User::MEDIA_COLLECTION);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
