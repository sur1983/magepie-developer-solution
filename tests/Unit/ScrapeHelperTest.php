<?php

namespace Tests\Unit;

use App\ScrapeHelper;
use Tests\TestCase;

/**
 * Class ScrapeHelperTest.
 *
 * @covers \App\ScrapeHelper
 */
final class ScrapeHelperTest extends TestCase
{
    private ScrapeHelper $scrapeHelper;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->scrapeHelper = new ScrapeHelper();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->scrapeHelper);
    }

    public function testFetchDocument(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testChildDomFilter(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
