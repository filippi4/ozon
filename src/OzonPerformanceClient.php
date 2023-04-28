<?php

namespace Filippi4\Ozon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OzonPerformanceClient
{
    private const CONNECT_TIMEOUT = 5;
    private const TIMEOUT = 120;

    private const URL = 'https://performance.ozon.ru/';

    private const DEFAULT_HEADER = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    private const DEFAULT_OPTIONS = [
        'headers' => self::DEFAULT_HEADER
    ];

    protected ?array $config;

    /**
     * ClientHint constructor.
     */
    public function __construct()
    {
        $this->config = null;
    }

    /**
     * @throws ValidationException
     */
    protected function validateKeys(array $keys): void
    {
        $validator = Validator::make($keys, [
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->messages()->getMessages());
        }
    }

    /**
     * Create GET request to bank API
     *
     * @param string|null $uri
     * @param array $params
     * @param bool $is_json
     * @return OzonResponse
     */
    protected function getResponse(string $uri = null, array $params = [], bool $is_json = true): OzonResponse
    {
        $full_path = self::URL . $uri;

        if (!empty($params)) {
            $full_path .= '?' . $this->httpBuildQuery($params);
        }

        $options = self::DEFAULT_OPTIONS;
        $options['connect_timeout'] = self::CONNECT_TIMEOUT;
        $options['timeout'] = self::TIMEOUT;
        $options['headers']['Authorization'] = 'Bearer ' . Token::create($this->config);

        return OzonRequest::makeRequest($full_path, $options, 'get', $is_json);
    }

    /**
     * Create POST request to bank API
     *
     * @param string|null $uri
     * @param array $params
     * @return OzonResponse
     */
    protected function postResponse(string $uri = null, array $params = []): OzonResponse
    {
        $full_path = self::URL . $uri;
        $options = self::DEFAULT_OPTIONS;

        $options['connect_timeout'] = self::CONNECT_TIMEOUT;
        $options['timeout'] = self::TIMEOUT;

        $options['headers']['Authorization'] = 'Bearer ' . Token::create($this->config);

        if (!empty($params)) {
            $options['json'] = $params;
        }

        return OzonRequest::makeRequest($full_path, $options, 'post');
    }

    private function httpBuildQuery(array $params): string
    {
        $query = '';
        foreach ($params as $key => $value) {
            $value = is_array($value) ? $value : [$value];
            if (is_array($value)) {
                foreach ($value as $subValue) {
                    $query .= $key . '=' . $subValue . '&';
                }
            } else {
                $query .= $key . '=' . $value . '&';
            }
        }
        return rtrim($query, '&');
    }

    public function forgetToken(): void
    {
        Token::expire();
    }
}
