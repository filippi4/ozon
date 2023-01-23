<?php

namespace Filippovk997\Ozon;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OzonClient
{
    private const SELLER_URL = 'https://api-seller.ozon.ru/';

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
            'api_key' => 'required|string',
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
     * @return OzonResponse
     */
    protected function getResponse(string $uri = null, array $params = []): OzonResponse
    {
        $full_path = self::SELLER_URL . $uri;
        $options = self::DEFAULT_OPTIONS;

        $options['headers']['Client-Id'] = $this->config['client_id'];
        $options['headers']['Api-Key'] = $this->config['api_key'];

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
        $full_path = self::SELLER_URL . $uri;
        $options = self::DEFAULT_OPTIONS;

        $options['headers']['Client-Id'] = $this->config['client_id'];
        $options['headers']['Api-Key'] = $this->config['api_key'];

        if (count($params)) {
            $options['json'] = $params;
        }

        return OzonRequest::makeRequest($full_path, $options, 'post');
    }
}
