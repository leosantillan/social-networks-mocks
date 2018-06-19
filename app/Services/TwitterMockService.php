<?php

namespace App\Services;

use App\Helpers;
use Carbon\Carbon;

class TwitterMockService extends TwitterOAuthMockService
{
    /**
     * Handle Mock Response.
     *
     * @param   String  $uri
     * @param   Array   $options
     * @param   String  $method
     * @return  String
     */
    public function handleMockResponse($uri, array $options = [], $method = 'GET')
    {
        $image_host = getenv('IMAGE_HOST');
        $response = '';
        switch ($method) {
            case 'GET':
                $uri_parts = explode('/', $uri);
                $ep = end($uri_parts);

                $mock = (ctype_digit($ep)) ? 'last_tweets' : $ep;

                switch ($mock) {
                    case 'verify_credentials':
                        $faker = \App\Helpers\FakerHelper::getInstance(date("Ymd"));
                        $first_name = $faker->firstNameMale;
                        $last_name = $faker->lastName;
                        $image = \App\Helpers\FakerHelper::generateInitialAvatar($faker, $first_name, $last_name, '334');

                        $response = [
                            'id' => $faker->unique()->randomNumber(),
                            'followers_count' => $faker->numberBetween(50000, 5000000),
                            'screen_name' => $first_name . '_' . $last_name,
                            'name' => $first_name,
                            'email' => strtolower($first_name) . '.' . strtolower($last_name) . '@' . $faker->freeEmailDomain,
                            'profile_image_url' => $image['image_url'],
                            'verified' => $faker->boolean
                        ];
                        break;

                    case 'last_tweets':
                        $faker = \App\Helpers\FakerHelper::getInstance($ep);

                        $response = [];
                        for ($i = 0; $i < 20; $i++) {
                            $new_id = $faker->unique()->randomNumber();
                            $faker_stats = \App\Helpers\FakerHelper::getInstance($new_id);
                            $temp = [
                                'retweet_count' => $faker_stats->numberBetween(5000, 50000),
                                'favorite_count' => $faker_stats->numberBetween(5000, 50000)
                            ];
                            $response[] = $temp;
                        }
                        break;
                }
                break;
            case 'POST':
                $response = '{"message": "POST request not allowed"}';
                break;
            default:
                $response = '{"message": "Invalid request"}';
                break;
        }

        return $response;

    }
}
