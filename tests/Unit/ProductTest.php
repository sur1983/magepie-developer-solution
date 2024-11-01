<?php

namespace Tests\Unit;

use App\Product;
use Tests\TestCase;

/**
 * Class ProductTest.
 *
 * @covers \App\Product
 */
final class ProductTest extends TestCase
{
    private Product $product;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->product = new Product();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->product);
    }

    public function testParseProductData(): void
    {
        /** @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}
