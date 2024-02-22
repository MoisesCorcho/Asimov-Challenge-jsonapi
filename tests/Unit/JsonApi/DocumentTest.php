<?php

namespace Tests\Unit\JsonApi;

use App\JsonApi\Document;
use PHPUnit\Framework\TestCase;

class DocumentTest extends TestCase
{
    /** @test */
    public function can_create_json_api_documents()
    {
        $document = Document::type('appointments')
            ->id('1')
            ->attributes([
                'date' => '2025-01-01'
            ])
            ->toArray();

        $expected = [
            'data' => [
                'type' => 'appointments',
                'id' => '1',
                'attributes' => [
                    'date' => '2025-01-01'
                ]
            ]
        ];

        $this->assertEquals($expected, $document);
    }
}
