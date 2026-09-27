<?php

namespace Tests;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /**
     * Seed the verified site content (stock images are skipped during tests).
     */
    protected function seedSite(): void
    {
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Count occurrences of a regular expression in a response body.
     */
    protected function countMatches(TestResponse $response, string $pattern): int
    {
        return preg_match_all($pattern, (string) $response->getContent());
    }
}
