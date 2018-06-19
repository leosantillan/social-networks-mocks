<?php

namespace App\Services;

use GuzzleHttp\Psr7\Response;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7;

class GuzzleHttpMockService extends \GuzzleHttp\Client
{
    /**
     * Returns
     *
     * @param   String  $method
     * @param   String  $uri
     * @param   Array   $options
     * @return void
     */
    public function request($method, $uri, array $options = [])
    {
        $json_data = $this->handleMockResponse($method, $uri, $options);

        /*
         * Simpler Response
         * $response = new Response(200, ['Content-Type' => 'application/json'], $data);
         * return $response;
         */

        $body = Psr7\stream_for($json_data);
        $mock = new MockHandler([
            new Response(200, [], $body),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $response = $client->request($method, $uri);

        return $response;
    }
}
