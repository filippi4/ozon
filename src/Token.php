<?php

namespace Filippi4\Ozon;

class Token extends OzonPerformanceClient
{
    /**
     * @var array<string, string>
     */
    private static array $token = [];

    /**
     * @var array<float, float>
     */
    private static array $createTime = [];

    /**
     * @var array<int, int>
     */
    private static array $expiresIn = [];

    private static function needCreateOrUpdate(string $key): bool
    {
        return empty(self::$token[$key]) || self::isExpired($key);
    }

    private static function isExpired(string $key): bool
    {
        if (empty(self::$createTime[$key]) || empty(self::$expiresIn[$key])) {
            return true;
        }
        return ((microtime(true) - self::$createTime[$key]) > self::$expiresIn[$key]);
    }

    public static function create(array $params): string
    {
        $key = json_encode($params);
        if (!self::needCreateOrUpdate($key)) {
            return self::$token[$key];
        }

        $full_path = self::URL . 'client/token';
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];

        $params['grant_type'] = 'client_credentials';
        $options['json'] = $params;

        self::$createTime[$key] = microtime(true);
        $responseData = (new OzonData(OzonRequest::makeRequest($full_path, $options, 'post')))->data;
        self::$expiresIn[$key] = $responseData->expires_in;
        self::$token[$key] = $responseData->access_token;

        return self::$token[$key];
    }

    static public function expire(?array $params = null): void
    {
        if ($params === null) {
            self::$token = [];
        } else {
            unset(self::$token[json_encode($params)]);
        }
    }
}