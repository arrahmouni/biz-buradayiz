<?php

namespace Modules\Auth\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Models\User;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedFakeUsers();
    }

    private function seedFakeUsers(): void
    {
        User::factory()
        ->count(10)
        ->create();
    }
}
