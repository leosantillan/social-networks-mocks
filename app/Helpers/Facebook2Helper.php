<?php

namespace App\Helpers;

use App\Models\FacebookAccount;
use App\Models\FacebookPage;

use Exception;

use App\Exceptions\FacebookException;

use Carbon\Carbon;

class Facebook2Helper extends SocialNetworks2Helper
{
    private $client;
    private $endpoint;
    private $facebookAccount;
    private $facebookPage;
    private $facebook_app_id;
    private $facebook_app_secret;

    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
        $this->endpoint            = 'https://graph.facebook.com/v3.0/';
        $this->facebook_app_id     = getenv('FACEBOOK_APP_ID');
        $this->facebook_app_secret = getenv('FACEBOOK_APP_SECRET');
    }

    public function setPage(FacebookPage $facebookPage)
    {
        $this->facebookPage = $facebookPage;
    }

    public function getPageData()
    {
        $response = $this->client->request('GET', $this->endpoint . $this->facebookPage->page_id, [
            'query' => [
                'fields'       => 'fan_count',
                'access_token' => $this->facebookPage->page_access_token
            ]
        ]);

        $responseBody = $this->handleResponse($response);

        return $responseBody;
    }
}
