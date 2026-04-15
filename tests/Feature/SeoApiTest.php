<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Seo\Database\Seeders\SeoStaticPagesSeeder;
use Tests\TestCase;

class SeoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SeoStaticPagesSeeder::class);
    }

    public function test_static_seo_endpoint_returns_payload(): void
    {
        $response = $this->getJson('/api/v1/seo/static/home');

        $response->assertOk();
        $response->assertJsonPath('data.subject.kind', 'static');
        $response->assertJsonPath('data.subject.key', 'home');
        $response->assertJsonStructure([
            'data' => [
                'subject',
                'locale',
                'meta' => [
                    'meta_title',
                    'meta_description',
                    'meta_keywords',
                    'og_title',
                    'og_description',
                    'og_image',
                    'robots',
                    'canonical_url',
                ],
            ],
        ]);
    }

    public function test_static_unknown_key_returns_404(): void
    {
        $this->getJson('/api/v1/seo/static/does-not-exist')->assertNotFound();
    }
}
