<?php

namespace KFilippovk\Ozon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OzonPerformanceClient
{
    private const URL = 'https://performance.ozon.ru';

    private const DEFAULT_HEADER = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    private const DEFAULT_OPTIONS = [
        'headers' => self::DEFAULT_HEADER
    ];

    protected $config;

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

    private function getToken(): string
    {
        $full_path = self::URL . 'api/client/token';
        $options = self::DEFAULT_OPTIONS;

        $params = $this->config;
        $params['grant_type'] = 'client_credentials';
        $options['json'] = $params;

        return (new OzonData(OzonRequest::makeRequest($full_path, $options, 'post')))->data
            ->access_token; 
    }

    /**
     * Create GET request to bank API
     *
     * @param string|null $uri
     * @param array $params
     * @return OzonResponse
     */
    protected function getResponse(string $uri = null, array $params = []): OzonResponse
    {
        $full_path = self::URL . $uri;
        $options = self::DEFAULT_OPTIONS;

        $options['Authorization'] = 'Bearer ' . $this->getToken();

        if (count($params)) {
            $full_path .= '?' . http_build_query($params);
        }

        return OzonRequest::makeRequest($full_path, $options, 'get');
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

        $options['Authorization'] = 'Bearer ' . $this->getToken();

        if (count($params)) {
            $options['json'] = $params;
        }

        return OzonRequest::makeRequest($full_path, $options, 'post');
    }
}
