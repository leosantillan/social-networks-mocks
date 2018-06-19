<?php

namespace App\Helpers;

use App\Models\TwitterAccount;

use App\Exceptions\TwitterException;

use Carbon\Carbon;

class Twitter2Helper extends SocialNetworks2Helper
{
    private $twitterAccount;
    private $connection;

    public function __construct(\Abraham\TwitterOAuth\TwitterOAuth $twitterOAuth)
    {
        $this->connection = $twitterOAuth;
    }

    public function setConnection($token, $token_secret)
    {
        $this->connection->setOauthToken($token, $token_secret);
        $this->connection->setTimeouts(30, 60);
        $this->connection->setDecodeJsonAsArray(true);
    }

    public function setAccount(TwitterAccount $twitterAccount)
    {
        $this->twitterAccount = $twitterAccount;
        $this->setConnection($this->twitterAccount->twitter_token, $this->twitterAccount->twitter_token_secret);
    }

    public function getAccountData()
    {
        $result = $this->connection->get('account/verify_credentials');
        return $result;
    }
}
