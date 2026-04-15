<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Seo\Database\Seeders\SeoStaticPagesSeeder;
use Modules\Seo\Models\Seo;
use Modules\Seo\Models\SeoStaticPage;
use Tests\TestCase;

class SeoMorphUniqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_seo_for_same_morph_fails_at_database(): void
    {
        $this->seed(SeoStaticPagesSeeder::class);

        $page = SeoStaticPage::query()->where('key', 'home')->firstOrFail();

        $first = new Seo;
        $first->model()->associate($page);
        $first->save();

        $second = new Seo;
        $second->model()->associate($page);

        $this->expectException(QueryException::class);
        $second->save();
    }
}
