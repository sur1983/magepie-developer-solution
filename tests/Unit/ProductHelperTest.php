<?php

namespace Tests\Unit;

use App\ProductHelper;
use Tests\TestCase;

/**
 * Class ProductHelperTest.
 *
 * @covers \App\ProductHelper
 */
final class ProductHelperTest extends TestCase
{
    private ProductHelper $productHelper;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->productHelper = new ProductHelper();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->productHelper);
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

    public function testCreateArrayObject(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
