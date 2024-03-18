<?php

namespace Tests;

use Illuminate\Testing\TestResponse;

trait MakesJsonApiRequests
{

    protected $jsonApiHeaders = false;

    public function withoutJsonApiHeaders()
    {
        $this->jsonApiHeaders = true;
    }

    public function json($method, $uri, array $data = [], array $headers = [], $options = 0): TestResponse
    {
        if ( ! $this->jsonApiHeaders ) {
            $headers['accept'] = 'application/vnd.api+json';

            if ($method === 'POST' || $method === 'PATCH') {
                $headers['content-type'] = 'application/vnd.api+json';
            }
        }

        return parent::json($method, $uri, $data, $headers, $options);
    }

}
