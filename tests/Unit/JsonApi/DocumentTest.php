<?php

namespace Tests\Unit\JsonApi;

use Mockery;
use Tests\TestCase;
use App\JsonApi\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_json_api_documents()
    {
        $category = Mockery::mock('category', function ($mock) {
            $mock->shouldReceive('getResourceType')->andReturn('categories');
            $mock->shouldReceive('getRouteKey')->andReturn('1');
        });

        $document = Document::type('appointments')
            ->id('1')
            ->attributes([
                'date' => '2025-01-01'
            ])->relationships([
                'category' => $category
            ])->toArray();

        $expected = [
            'data' => [
                'type' => 'appointments',
                'id' => '1',
                'attributes' => [
                    'date' => '2025-01-01'
                ],
                'relationships' => [
                    'category' => [
                        'data' => [
                            'type' => 'categories',
                            'id' => '1'
                        ]
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $document);
    }
}
