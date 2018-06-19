<?php

namespace App\Services;

use App\Helpers;
use Carbon\Carbon;

class FacebookMockService extends GuzzleHttpMockService
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
        $resp = '';
        switch ($method) {
            case 'GET':
                $uri_parts = explode('/', $uri);
                $ep = end($uri_parts);

                if (ctype_digit($ep)) {
                    $mock = ($options['query']['fields'] === 'fan_count') ? 'getPageData' : 'getPage';
                } else {
                    $ep2 = explode('_', $ep);
                    $mock = (ctype_digit($ep2[0])) ? 'getPostInteractions' : $ep;
                }

                switch ($mock) {
                    case 'getPageData':
                        $faker = \App\Helpers\FakerHelper::getInstance($ep);

                        $resp = [
                            'fan_count' => $faker->numberBetween(50000, 5000000),
                            'id' => $ep
                        ];
                        break;

                    case 'picture':
                        $page_id = $uri_parts[count($uri_parts) - 2];
                        $faker = \App\Helpers\FakerHelper::getInstance($page_id);
                        $image = \App\Helpers\FakerHelper::generateInitialAvatar($faker, $faker->firstNameMale, $faker->lastName, '334');

                        $resp = [
                            'data' => [
                                'height' => 334,
                                'is_silhouette' => $faker->boolean,
                                'url' => $image['image_url'],
                                'width' => 334
                            ]
                        ];
                        break;

                    case 'me':
                        $faker = \App\Helpers\FakerHelper::getInstance($options['query']['access_token']);
                        $fn = $faker->firstNameMale;
                        $ln = $faker->lastName;

                        $resp = [
                            'id' => $faker->unique()->randomNumber(),
                            'is_verified' => $faker->boolean ? 'true' : 'false',
                            'email' => strtolower($fn) . '.' . strtolower($ln) . '@' . $faker->freeEmailDomain,
                            'first_name' => $fn,
                            'last_name' => $ln
                        ];
                        break;

                    case 'getPage':
                        $faker = \App\Helpers\FakerHelper::getInstance($ep);
                        $first_name = $faker->firstNameMale;
                        $last_name = $faker->lastName;
                        $name = $first_name . ' ' . $last_name;
                        $image['image_url'] = $image_host . substr($faker->sha256(), 0, 40) . '.png';

                        $resp = [
                            'id' => $ep,
                            'name' => $name,
                            'fan_count' => $faker->numberBetween(50000, 5000000),
                            'is_eligible_for_branded_content' => $faker->boolean,
                            'access_token' => $faker->sha256(),
                            'link' => 'https://www.facebook.com/' . str_replace(' ', '-', $name) . '-' . $ep . '/',
                            'picture' => [
                                'data' => [
                                    'height' => 100,
                                    'is_silhouette' => $faker->boolean,
                                    'url' => $image['image_url'],
                                    'width' => 100
                                ]
                            ]
                        ];
                        break;

                    case 'accounts':
                        $seed = date('Ymd');
                        $faker = \App\Helpers\FakerHelper::getInstance($seed);

                        $resp = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {
                            $new_id = $faker->unique()->randomNumber();

                            $faker_page = \App\Helpers\FakerHelper::getInstance($new_id);
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
                                'is_eligible_for_branded_content' => $faker_page->boolean,
                                'picture' => [
                                    'data' => [
                                      'height' => 100,
                                      'is_silhouette' => $faker_page->boolean,
                                      'url' => $image['image_url'],
                                      'width' => 100
                                    ]
                                ],
                            ];

                            $resp['data'][] = $temp;

                        }
                        break;

                    case 'post_engagements,post_impressions,post_impressions,post_impressions_unique':
                        $post_id = $uri_parts[count($uri_parts) - 3];
                        $faker = \App\Helpers\FakerHelper::getInstance($post_id);

                        $resp = [
                            'data' => [
                                [
                                    'name' => 'post_engagements',
                                    'period' => 'lifetime',
                                    'values' => [
                                        [
                                            'value' => $faker->numberBetween(100, 9999)
                                        ]
                                    ],
                                    'title' => 'Lifetime Post Engagements',
                                    'description' => 'Lifetime => Number of times people have engaged in certain ways with your Page post, for example by commenting on, liking, sharing, or clicking upon particular elements of the post.',
                                    'id' => '842333732594667_842804075880966/insights/post_engagements/lifetime'
                                ],
                                [
                                    'name' => 'post_impressions',
                                    'period' => 'lifetime',
                                    'values' => [
                                        [
                                            'value' => $faker->numberBetween(100, 9999)
                                        ]
                                    ],
                                    'title' => 'Lifetime Post Total Impressions',
                                    'description' => 'Lifetime => The number of impressions of your Page post. (Total Count)',
                                    'id' => '842333732594667_842804075880966/insights/post_impressions/lifetime'
                                ],
                                [
                                    'name' => 'post_impressions_unique',
                                    'period' => 'lifetime',
                                    'values' => [
                                        [
                                            'value' => $faker->numberBetween(100, 9999)
                                        ]
                                    ],
                                    'title' => 'Lifetime Post Total Reach',
                                    'description' => 'Lifetime => The total number of people your Page post was served to. (Unique Users)',
                                    'id' => '842333732594667_842804075880966/insights/post_impressions_unique/lifetime'
                                ]
                            ]
                        ];
                        break;

                    case 'getPostInteractions':
                        $faker = \App\Helpers\FakerHelper::getInstance($ep);

                        $resp = [
                            'likes' => [
                                'data' => [
                                ],
                                'summary' => [
                                    'total_count' => $faker->numberBetween(100, 99999),
                                    'can_like' => $faker->boolean,
                                    'has_liked' => $faker->boolean
                                ]
                            ],
                            'comments' => [
                                'data' => [
                                ],
                                'summary' => [
                                    'order' => 'chronological',
                                    'total_count' => $faker->numberBetween(100, 99999),
                                    'can_comment' => $faker->boolean
                                ]
                            ],
                            'shares' => [
                                'count' => $faker->numberBetween(100, 99999)
                            ],
                            'id' => '842333732594667_842748695886504'
                        ];
                        break;

                    case 'feed':
                        $post_types = [
                            'photo',
                            'video',
                            'link',
                            'status'
                        ];

                        $page_id = $uri_parts[count($uri_parts) - 2];
                        $faker = \App\Helpers\FakerHelper::getInstance($page_id);

                        $resp = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {

                            $post_type_index = $faker->numberBetween(0, count($post_types) - 1);
                            $post_type = $post_types[$post_type_index];

                            $temp = [
                                'id' => $page_id . '_' . $faker->unique()->randomNumber(),
                                'type' => $post_type,
                                'created_time' => Carbon::now()->subDays($i)->toRfc3339String()
                            ];

                            $resp['data'][] = $temp;
                        }
                        break;

                    case 'search':
                        $faker = \App\Helpers\FakerHelper::getInstance($options['query']['q']);
                        $name = $faker->words($nbWords = 2, $asText = true);
                        $name_array = explode(' ', $name);
                        $image['image_url'] = $image_host . substr($faker->sha256(), 0, 40) . '.png';

                        $resp = [
                            'data' => []
                        ];
                        for ($i = 0; $i < 10; $i++) {
                            $temp = [
                                'id' => $faker->unique()->randomNumber(),
                                'name' => $name,
                                'fan_count' => $faker->numberBetween(50000, 5000000),
                                'is_eligible_for_branded_content' => $faker->boolean,
                                'link' => 'https://www.facebook.com/' . str_replace(' ', '-', $name) . '/',
                                'picture' => [
                                    'data' => [
                                        'height' => 100,
                                        'is_silhouette' => $faker->boolean,
                                        'url' => $image['image_url'],
                                        'width' => 100
                                    ]
                                ]
                            ];

                            $resp['data'][] = $temp;
                        }
                        break;
                }
                break;

            case 'POST':
                $resp = ['id' => '842333732594667_842391725922201'];
                break;

            default:
                $resp = ['message' => 'Invalid request'];
                break;
        }

        return json_encode($resp);
    }
}
