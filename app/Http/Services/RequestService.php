<?php

namespace App\Http\Services;

use App\Http\Constants\RequestConstant;
use Illuminate\Support\Facades\Http;

class RequestService
{
    /**
     * @param string $url
     * @param string $method
     * @param int $timeoutSeconds
     * @param array $data
     * @return mixed
     */
    public static function request(string $url, string $method = 'GET', int $timeoutSeconds = 30, array $data = []): mixed
    {
        if ('GET' === $method) {
            return Http::timeout($timeoutSeconds)
                ->{$method}(RequestConstant::URL_REQUEST . $url)->json();
        }
        return Http::timeout($timeoutSeconds)
            ->attach($data)
            ->{$method}(RequestConstant::URL_REQUEST . $url)->json;
    }
}
