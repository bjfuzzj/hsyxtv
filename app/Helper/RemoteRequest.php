<?php

namespace App\Helper;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Log;

class RemoteRequest
{
    const GET    = 'get';
    const DELETE = 'delete';
    const HEAD   = 'head';
    const POST   = 'post';
    const PATCH  = 'patch';
    const PUT    = 'put';

    const ERROR_SYSTEM = '500';

    private static function request($url, $body, $method, $config = [])
    {
        $client   = new GuzzleClient($config);
        $response = $client->request($method, $url, $body);
        if (200 != $response->getStatusCode()) {
            Log::error('==RemoteRequest异常==', \func_get_args());
            return false;
        }
        return json_decode((string)$response->getBody(), true);
    }

    public static function get($url, array $params = [], array $config = [])
    {
        return self::request($url, ['query' => $params], self::GET, $config);
    }

    public static function post($url, array $params = [], array $config = [])
    {
        return self::request($url, ['form_params' => $params], self::POST, $config);
    }

    public static function postBody($url, $body, array $config = [])
    {
        return self::request($url, ['body' => $body], self::POST, $config);
    }

}
