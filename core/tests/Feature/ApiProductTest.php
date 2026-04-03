<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiProductTest extends TestCase
{
    /**
     * Public API: product list returns JSON and 200.
     */
    public function test_products_index_returns_success(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data', 'meta' => ['current_page', 'last_page', 'per_page', 'total']]);
        $this->assertTrue($response->json('success'));
    }

    /**
     * API health check for monitoring/CI.
     */
    public function test_health_returns_ok(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }
}
