<?php

namespace Modules\Verimor\Database\Seeders;

use Illuminate\Database\Seeder;

class VerimorDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            VerimorCallEventSeeder::class,
        ]);

    }
}
