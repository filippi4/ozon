<?php

namespace Filippi4\Ozon;

class Token
{
    private static ?string $token = null;
    private static ?float $createTime = null;
    private static ?int $expiresIn = null;

    private static function needCreateOrUpdate(): bool
    {
        return (self::$token === null || self::isExpired());
    }

    private static function isExpired(): bool
    {
        return ((microtime(true) - self::$createTime) > self::$expiresIn);
    }

    public static function create(array $params): string
    {
        if (!self::needCreateOrUpdate()) {
            return self::$token;
        }

        $full_path = 'https://performance.ozon.ru/api/client/token';
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]
        ];

        $params['grant_type'] = 'client_credentials';
        $options['json'] = $params;

        self::$createTime = microtime(true);
        $responseData = (new OzonData(OzonRequest::makeRequest($full_path, $options, 'post')))->data;
        self::$expiresIn = $responseData->expires_in;
        self::$token = $responseData->access_token;

        return self::$token;
    }

    static public function expire(): void
    {
        self::$token = null;
    }
}
