<?php


namespace KFilippovk\Ozon;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use KFilippovk\Ozon\Exceptions\OzonHttpException;
use Throwable;

class OzonRequest
{
    /**
     * @var null|OzonRequest
     */
    private static ?OzonRequest $instance = null;
    private Client $httpClient;

    /**
     * gets the instance via lazy initialization (created on first usage)
     */
    private static function getInstance(): OzonRequest
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * is not allowed to call from outside to prevent from creating multiple instances,
     * to use the singleton, you have to obtain the instance from OzonRequest::getInstance() instead
     */
    private function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Create POST/GET request to Ozon API
     *
     * @param string $url
     * @param array $options
     * @param string $method
     * @return OzonResponse
     * @throws OzonHttpException
     */

    public static function makeRequest(string $url, array $options, string $method = 'get', $is_json = true): OzonResponse
    {
        return new OzonResponse(
            $method === 'get'
                ? self::runGetRequest($url, $options)
                : self::runPostRequest($url, $options),
            $is_json
        );
    }

    /**
     * Create GET request to Ozon API
     *
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws OzonHttpException
     */
    private static function runGetRequest(string $url, array $options): ResponseInterface
    {
        try {
            return self::getInstance()->getHttpClient()->get($url, $options);
        } catch (Throwable $exception) {
            throw new OzonHttpException($exception);
        }
    }

    /**
     * Create POST request to Ozon API
     *
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws OzonHttpException
     */
    private static function runPostRequest(string $url, array $options): ResponseInterface
    {
        try {
            return self::getInstance()->getHttpClient()->post($url, $options);
        } catch (Throwable $exception) {
            throw new OzonHttpException($exception);
        }
    }

    private function getHttpClient(): Client
    {
        return $this->httpClient;
    }
}
