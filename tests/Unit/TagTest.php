<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TagTest extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    /**
     * Test index.
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->json('GET', '/api/tags');
        $response
            ->assertStatus(200)
            ->assertJson([
                'current_page' => 1
            ]);

        $response = $this->json('GET', '/api/tags?q=test');
        $response
            ->assertStatus(200)
            ->assertJson([
                'current_page' => 1
            ]);
    }
}
