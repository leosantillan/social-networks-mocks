<?php

namespace App\Helpers;

use App\Models\InstagramAccount;

use App\Exceptions\InstagramException;

use Carbon\Carbon;

class Instagram2Helper extends SocialNetworks2Helper
{
    private $client;
    private $endpoint;
    private $endpoint_old;
    private $instagramAccount;
    private $instagramBusinessAccountId;

    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
        $this->endpoint_old = 'https://api.instagram.com/v1/';
        $this->endpoint = 'https://graph.facebook.com/v3.0/';
    }

    public function setAccount(InstagramAccount $instagramAccount)
    {
        $this->instagramAccount = $instagramAccount;
        $this->instagramBusinessAccountId = $instagramAccount->business_account_id;
    }

    public function setBusinessAccountId($instagramBusinessAccountId)
    {
        $this->instagramBusinessAccountId = $instagramBusinessAccountId;
    }

    public function getPost($instagramPostId)
    {
        $response = $this->client->request('GET', $this->endpoint . '/' . $instagramPostId . '/insights', [
            'query' => [
                'metric' => 'engagement,impressions,reach,saved',
                'access_token'  => $this->instagramAccount->instagram_token
            ]
        ]);

        $responseBody = $this->handleResponse($response);

        foreach ($responseBody['data'] as $_data) {
            $postData[$_data['name']] = $_data['values'][0]['value'];
        }

        return $postData;
    }
}
