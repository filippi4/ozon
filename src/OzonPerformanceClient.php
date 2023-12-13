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

    /**
     * Get file
     *
     * @param string|null $uri
     * @param int $quantityOfCampaigns
     * @param array $params
     * @return mixed
     */
    protected function getFile(string $uri = null, int $quantityOfCampaigns, array $params = []): mixed
    {
        $full_path = self::URL . $uri;

        $ch = curl_init($full_path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . Token::create($this->config), "Content-Type: text/csv"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if ($quantityOfCampaigns > 1) {
            $zip = new \ZipArchive;
            file_put_contents(base_path() . '/file.zip', curl_exec($ch));
            curl_close($ch);
            $res = $zip->open(base_path() . '/file.zip');
            $fileNames = [];

            if ($res === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileNames[] = $zip->getNameIndex($i);
                }
                $zip->extractTo(base_path() . '/');
                $zip->close();
                try {
                    unlink(base_path() . '/file.zip');
                } catch (\Throwable $e) {
                    dump($e->getMessage());
                }
            }
        } else {
            file_put_contents(base_path() . '/file.csv', curl_exec($ch));
            preg_match("/\d+/", file_get_contents(base_path() . '/file.csv'), $matches);
            if (empty($matches)) {
                unlink(base_path() . '/file.csv');
                $fileNames = [];
            } else {
                rename(base_path() . '/file.csv', base_path() . '/' . $matches[0] . '.csv');
                $fileNames = [$matches[0] . '.csv'];
            }
            curl_close($ch);
        }
        return $fileNames;
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