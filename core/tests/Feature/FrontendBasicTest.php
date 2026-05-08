<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendBasicTest extends TestCase
{
    /**
     * Test homepage load
     *
     * @return void
     */
    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test products page
     *
     * @return void
     */
    public function test_products_page_loads_successfully()
    {
        $response = $this->get('/all/products');
        $response->assertStatus(200);
    }

    /**
     * Test cart page
     *
     * @return void
     */
    public function test_cart_page_loads_successfully()
    {
        $response = $this->get('/cart');
        $response->assertStatus(200);
    }
}
