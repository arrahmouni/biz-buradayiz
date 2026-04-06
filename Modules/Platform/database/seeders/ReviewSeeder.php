<?php

namespace Modules\Platform\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Platform\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Each review gets a random {@see \Modules\Platform\Enums\ReviewStatus} from the factory.
     */
    public function run(): void
    {
        Review::factory()->count(10)->create();
    }
}
