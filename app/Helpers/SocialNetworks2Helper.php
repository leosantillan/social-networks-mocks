<?php

namespace App\Helpers;

class SocialNetworks2Helper
{
    protected function handleResponse($response)
    {
        $responseBody = (string) $response->getBody();
        $responseBody = \GuzzleHttp\json_decode($responseBody, true);
        return $responseBody;
    }
}
