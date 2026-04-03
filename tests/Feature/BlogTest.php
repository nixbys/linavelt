<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_is_publicly_accessible(): void
    {
        $response = $this->get(route('blog.index'));

        $response->assertStatus(200);
    }
}
