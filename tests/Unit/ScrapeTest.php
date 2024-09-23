<?php

namespace Tests\Unit;

use App\Scrape;
use Tests\TestCase;

/**
 * Class ScrapeTest.
 *
 * @covers \App\Scrape
 */
final class ScrapeTest extends TestCase
{
    private Scrape $scrape;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->scrape = new Scrape();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->scrape);
    }

    public function testWriteOutputJson(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testExecute(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
