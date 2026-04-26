<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Ensure a valid encryption key exists for the test app without committing
     * a literal APP_KEY (avoids GitGuardian / secret-scan false positives on phpunit.xml).
     */
    public function createApplication(): Application
    {
        $key = $_ENV['APP_KEY'] ?? getenv('APP_KEY');
        if (! is_string($key) || $key === '') {
            $key = 'base64:'.base64_encode(hash('sha256', 'biz-buradayiz-phpunit-app-key-v1', true));
            $_ENV['APP_KEY'] = $key;
            putenv('APP_KEY='.$key);
        }

        return parent::createApplication();
    }
}
