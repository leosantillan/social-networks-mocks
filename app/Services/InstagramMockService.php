<?php

namespace App\Services;

use App\Helpers;
use Carbon\Carbon;

class InstagramMockService extends GuzzleHttpMockService
{
    /**
     * Handle Mock Response.
     *
     * @param   String  $method
     * @param   String  $uri
     * @param   Array   $options
     * @return  String
     */
    public function handleMockResponse($method, $uri, array $options = [])
    {
        $image_host = getenv('IMAGE_HOST');
        $response = '';
        switch ($method) {
            case 'GET':
                $uri_parts = explode('/', $uri);
                $ep = end($uri_parts);

                if (ctype_digit($ep)) {
                    if ($options['query']['fields'] == 'instagram_business_account') {
                        $mock = 'getPage';
                    } else if ($options['query']['fields'] == 'like_count,comments_count') {
                        $mock = 'getEngagement';
                    } else {
                        $mock = 'getAccountSocialData';
                    }
                } else {
                    if ($ep !== 'media') {
                        $mock = $ep;
                    } else {
                        $mock = ($options['query']['fields'] == 'id,caption,like_count,comments_count') ? 'media_search' : 'media_last';
                    }
                }

                switch ($mock) {
                    case 'me':
                        $faker = \App\Helpers\FakerHelper::getInstance($options['query']['access_token']);

                        $response = [
                            'id' => $faker->unique()->randomNumber(),
                            'name' => $faker->firstNameMale . ' ' . $faker->lastName
                        ];
                        break;

                    case 'getAccountSocialData':
                        $faker = \App\Helpers\FakerHelper::getInstance($options['query']['access_token']);
                        $image = \App\Helpers\FakerHelper::generateInitialAvatar($faker, $faker->firstNameMale, $faker->lastName, '334');

                        $response = [
                            'followers_count' => $faker->numberBetween(50000, 5000000),
                            'username' => $faker->firstNameMale . $faker->lastName,
                            'profile_picture_url' => $image['image_url']
                        ];
                        break;

                    case 'getPage':
                        $faker = \App\Helpers\FakerHelper::getInstance($ep);

                        $response = [
                            'instagram_business_account' => [
                                'id' => $faker->numberBetween(50000, 5000000)
                            ],
                            'id' => $ep
                        ];
                        break;

                    case 'accounts':
                        $seed = date('Ymd');
                        $faker = \App\Helpers\FakerHelper::getInstance($seed);

                        $response = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {
                            $new_id = $faker->unique()->randomNumber();

                            $faker_page = \App\Helpers\FakerHelper::getInstance($new_id);
                            $iba = $faker_page->numberBetween(50000, 5000000);
                            $first_name = $faker->firstNameMale;
                            $last_name = $faker->lastName;
                            $name = $first_name . ' ' . $last_name;
                            $image['image_url'] = $image_host . substr($faker->sha256(), 0, 40) . '.png';

                            $temp = [
                                'id' => $new_id,
                                'name' => $name,
                                'fan_count' => $faker_page->numberBetween(50000, 5000000),
                                'access_token' => $faker_page->sha256(),
                                'link' => 'https://www.facebook.com/' . str_replace(' ', '-', $name) . '-' . $new_id . '/',
                                'instagram_business_account' => [
                                    'id' => $iba
                                ],
                                'picture' => [
                                    'data' => [
                                        'height' => 100,
                                        'is_silhouette' => $faker_page->boolean,
                                        'url' => $image['image_url'],
                                        'width' => 100
                                    ]
                                ]
                            ];

                            $response['data'][] = $temp;
                        }
                        break;

                    case 'insights':
                        $post_id = $uri_parts[count($uri_parts) - 2];
                        $faker = \App\Helpers\FakerHelper::getInstance($post_id);

                        $response = [
                            'data' => [
                                [
                                    'name' => 'impressions',
                                    'period' => 'lifetime',
                                    'values' =>
                                    [
                                        [
                                            'value' => $faker->numberBetween(5000, 100000),
                                        ],
                                    ],
                                    'title' => 'Impressions',
                                    'description' => 'Total number of times the media object has been seen',
                                    'id' => '17855590849148465/insights/impressions/lifetime',
                                ],
                                [
                                    'name' => 'reach',
                                    'period' => 'lifetime',
                                    'values' =>
                                    [
                                        [
                                            'value' => $faker->numberBetween(5000, 100000),
                                        ],
                                    ],
                                    'title' => 'Reach',
                                    'description' => 'Total number of unique accounts that have seen the media object',
                                    'id' => '17855590849148465/insights/reach/lifetime',
                                ],
                                [
                                    'name' => 'engagement',
                                    'period' => 'lifetime',
                                    'values' =>
                                    [
                                        [
                                            'likes'     => $faker->numberBetween(5000, 50000),
                                            'comments'  => $faker->numberBetween(5000, 30000),
                                            'shares'    => $faker->numberBetween(5000, 50000),
                                        ],
                                    ],
                                    'title' => 'Engagement',
                                    'description' => 'Total number of unique accounts that have interacted with the media object',
                                    'id' => '17855590849148465/insights/engagement/lifetime',
                                ],
                                [
                                    'name' => 'saved',
                                    'period' => 'lifetime',
                                    'values' =>
                                    [
                                        [
                                            'value' => $faker->numberBetween(5000, 50000),
                                        ],
                                    ],
                                    'title' => 'Saved',
                                    'description' => 'Total number of unique accounts that have saved the media object',
                                    'id' => '17855590849148465/insights/saved/lifetime',
                                ],
                                [
                                    'name' => 'video_views',
                                    'period' => 'lifetime',
                                    'values' =>
                                    [
                                        [
                                            'value' => $faker->numberBetween(5000, 50000),
                                        ],
                                    ],
                                    'title' => 'Vide Views',
                                    'description' => 'Total number of unique accounts that have viewed the media object',
                                    'id' => '17855590849148465/insights/video_views/lifetime',
                                ],
                            ],
                        ];
                        break;

                    case 'getEngagement':
                        $post_id = $uri_parts[count($uri_parts) - 1];
                        $faker = \App\Helpers\FakerHelper::getInstance($post_id);

                        $response = [
                            'like_count' => $faker->numberBetween(5000, 500000),
                            'comments_count' => $faker->numberBetween(5000, 500000),
                        ];
                        break;

                    case 'media_last':
                        $media_types = [
                            'IMAGE',
                            'VIDEO',
                            'CAROUSEL_ALBUM'
                        ];

                        $account_id = $uri_parts[count($uri_parts) - 2];
                        $faker = \App\Helpers\FakerHelper::getInstance($account_id);

                        $response = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {
                            $media_type_index = $faker->numberBetween(0, count($media_types) - 1);
                            $media_type = $media_types[$media_type_index];

                            $temp = [
                                'media_type' => $media_type,
                                'id' => $faker->unique()->randomNumber(),
                                'timestamp' => Carbon::now()->subDays($i)->toRfc3339String(),
                            ];

                            $response['data'][] = $temp;
                        }
                        break;

                    case 'media_search':
                        $account_id = $uri_parts[count($uri_parts) - 2];
                        $faker = \App\Helpers\FakerHelper::getInstance($account_id);

                        $response = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {

                            $temp = [
                                'id' => $faker->unique()->randomNumber(),
                                'caption' => '#' . $name = $faker->words($nbWords = 1, $asText = true),
                                'like_count' => $faker->numberBetween(5000, 50000),
                                'comments_count' => $faker->numberBetween(5000, 30000)
                            ];

                            $response['data'][] = $temp;
                        }
                        break;

                    case 'search':
                        $faker = \App\Helpers\FakerHelper::getInstance($options['query']['q']);

                        $response = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {
                            $name = $faker->words($nbWords = 2, $asText = true);
                            $name_array = explode(' ', $name);
                            $image['image_url'] = $image_host . substr($faker->sha256(), 0, 40) . '.png';

                            $temp = [
                                'id' => $faker->unique()->randomNumber(),
                                'name' => $name,
                                'fan_count' => $faker->numberBetween(50000, 5000000),
                                'link' => 'https://www.facebook.com/' . str_replace(' ', '-', $name) . '/',
                                'instagram_business_account' => [
                                    'id' => $faker->unique()->randomNumber()
                                ],
                                'picture' =>
                                [
                                    'data' =>
                                    [
                                        'height' => 100,
                                        'is_silhouette' => $faker->boolean,
                                        'url' => $image['image_url'],
                                        'width' => 100
                                    ]
                                ]
                            ];

                            $response['data'][] = $temp;
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

        return json_encode($response);

    }
}
