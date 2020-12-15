<?php

declare(strict_types=1);

namespace app\Client;

use app\Cache\CacheStorage;
use app\Models\Post;
use app\Models\Posts;

class ApiClient
{
    private string $clientId;
    private string $email;
    private string $name;
    private string $url;

    public function __construct()
    {
        $configs = include('config/client.php');
        $config = $configs['supermetrics'];

        //var_dump($config); exit();

        $this->url = $config['url'];
        $this->clientId = $config['clientId'];
        $this->email = $config['email'];
        $this->name = $config['name'];
    }

    /**
     * POST register token https://api.supermetrics.com/assignment/register
     *
     * @return string
     */
    public function obtainToken(): string
    {
        // prepare the request
        $uri = $this->url . 'register';
        $payload = http_build_query([
            'client_id' => $this->clientId,
            'email' => $this->email,
            'name' => $this->name,
        ]);

        // build the curl request
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $uri);
        curl_setopt( $curlInit, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($curlInit, CURLOPT_POST, 1);
        curl_setopt($curlInit, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

        // process and return the response
        $response = curl_exec($curlInit);
        $response = json_decode($response, true);

        if (! isset($response['data'], $response['data']['sl_token'])) {
            exit('failed, exiting.');
        }

        // here's token to use in API requests
        return $response['data']['sl_token'];
    }

    /**
     * GET data from request
     *
     * @param string $endpoint
     * @param array $parameters
     *
     * @return string
     * @throws \HttpException
     */
    private function get(string $endpoint, array $parameters = []): string
    {
        $curlInit = curl_init();
        curl_setopt($curlInit, CURLOPT_URL, $this->url . $endpoint . '?' . http_build_query($parameters));
        curl_setopt( $curlInit, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

        if (! $response = curl_exec($curlInit)) {
            throw new \HttpException('Response error ' . $this->url . $endpoint);
        }

        return $response;
    }

    /**
     * Get monthly week number
     *
     * @param \DateTime $date
     *
     * @return int
     */
    private function weekOfMonth(\DateTime $date): int
    {
        $firstOfMonth = $date->format('Y-m-01');

        return (int) $date->format('W') - (int) date('W', strtotime($firstOfMonth)) + 1;
    }

    /**
     * Collect pages from https://api.supermetrics.com/assignment/posts
     * and save in Posts
     *
     * @param int $pageLimit
     *
     * @return Posts
     * @throws \Exception
     */
    public function getPosts(int $pageLimit = 10): Posts
    {
        $posts = [];
        $token = \unserialize((new CacheStorage())->get('cached_obtainToken'), [true]);

        for ($page = 1, $total = $pageLimit; $page <= $total; $page++) {
            $data = $this->getPostsPage($page, $token);
            foreach ($data as $item) {
                $createdTime = date_create($item->created_time);
                $posts[] = new Post(
                    $item->id,
                    $item->message,
                    $item->from_name,
                    $createdTime->format('F'),
                    (int) $createdTime->format('W'),  //TODO: if we need, use this "$this->weekOfMonth($createdTime)"
                    $createdTime->getTimestamp(),
                );
            }
        }

        return new Posts(...$posts);
    }

    /**
     * Get one page from https://api.supermetrics.com/assignment/posts
     *
     * @param int $page
     * @param string $token
     *
     * @return array
     * @throws \Exception
     */
    private function getPostsPage(int $page = 1, string $token = ''): array
    {
        try {
            $response = json_decode($this->get('posts', [
                'sl_token' => $token,
                'page' => $page
            ]), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw $e;
        }

        if (! isset($response->data->posts)) {
            throw new \HttpResponseException('Posts: data not found!');
        }

        return $response->data->posts;
    }
}
