<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterOAuthMockService extends TwitterOAuth
{
    private $api_key;
    private $api_secret;
    private $token;
    private $token_secret;
    private $timeout1;
    private $timeout2;
    private $decodedAsArray;
    
    /**
     * Returns
     *
     * @param   String  $api_key
     * @param   String  $api_secret
     * @param   String  $token
     * @param   String  $token_secret
     * @return void
     */
    public function __construct($api_key, $api_secret, $token = "", $token_secret = "")
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->token = $token;
        $this->token_secret = $token_secret;
    }
    
    public function get($uri, array $options = [])
    {
        return $this->handleMockResponse($uri, $options);
    }
    
    public function setTimeouts($val1, $val2)
    {
        $this->timeout1 = $val1;
        $this->timeout2 = $val2;
    }
    
    public function setDecodeJsonAsArray($val)
    {
        $this->decodedAsArray = (bool)$val;
    }
    
    public function getLastHttpCode()
    {
        return 200;
    }
}
