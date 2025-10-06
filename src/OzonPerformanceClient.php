<?php
namespace Filippi4\Ozon;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OzonPerformanceClient
{
    private const CONNECT_TIMEOUT = 5;
    private const TIMEOUT         = 120;

    protected const URL = 'https://api-performance.ozon.ru/';

    private const DEFAULT_HEADER = [
        'Accept'       => 'application/json',
        'Content-Type' => 'application/json',
    ];

    private const DEFAULT_OPTIONS = [
        'headers' => self::DEFAULT_HEADER,
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
            'client_id'     => 'required|string',
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

        if (! empty($params)) {
            $full_path .= '?' . $this->httpBuildQuery($params);
        }

        $options                             = self::DEFAULT_OPTIONS;
        $options['connect_timeout']          = self::CONNECT_TIMEOUT;
        $options['timeout']                  = self::TIMEOUT;
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
        $options   = self::DEFAULT_OPTIONS;

        $options['connect_timeout'] = self::CONNECT_TIMEOUT;
        $options['timeout']         = self::TIMEOUT;

        $options['headers']['Authorization'] = 'Bearer ' . Token::create($this->config);

        if (! empty($params)) {
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

        $fileNames = [];
        if ($quantityOfCampaigns > 1) {
            $zip      = new \ZipArchive;
            $fileName = Str::random(10) . '_file.zip';
            file_put_contents(storage_path() . '/' . $fileName, curl_exec($ch));
            curl_close($ch);
            $res = $zip->open(storage_path() . '/' . $fileName);

            if ($res === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileNames[] = $zip->getNameIndex($i);
                }
                $zip->extractTo(storage_path() . '/');
                $zip->close();
                try {
                    unlink(storage_path() . '/' . $fileName);
                } catch (\Throwable $e) {
                    dump($e->getMessage());
                }
            }
        } else {
            $contents = curl_exec($ch);
            preg_match("/\d+/", $contents, $matches);
            if (! empty($matches)) {
                $fileName = Str::random(10) . '_' . $matches[0] . '.csv';
                file_put_contents(storage_path() . '/' . $fileName, $contents);
                $fileNames = [$fileName];
            }
            curl_close($ch);
        }

        return $fileNames;
    }

    /**
     * Get file
     *
     * @param string|null $uri
     * @param int $quantityOfCampaigns
     * @param array $params
     * @return mixed
     */
    protected function getXlsxFile(string $uri = null, array $params = []): mixed
    {
        $full_path = self::URL . $uri;

        $ch = curl_init($full_path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . Token::create($this->config), "Content-Type: text/csv"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $fileName = '/' . Str::random(10) . '_file.xlsx';
        file_put_contents(storage_path() . $fileName, curl_exec($ch));
        curl_close($ch);

        return $fileName;
    }

    /**
     * Get json
     *
     * @param string|null $uri
     * @return mixed
     */
    public function getJson(string $uri = null): mixed
    {
        $full_path = self::URL . $uri;

        $ch = curl_init($full_path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . Token::create($this->config), "Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            dump('Error:' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
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
        Token::expire($this->config);
    }

    /**
     * Create POST request to bank API
     *
     * @param string|null $uri
     * @param array $params
     * @return mixed
     */
    protected function postResponseWithJson(string $uri = null, array $params = []): mixed
    {
        $full_path = self::URL . $uri;

        $response = Http::timeout(60)->withHeaders([
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . Token::create($this->config),
        ])->withBody(json_encode($params, JSON_UNESCAPED_UNICODE))->post($full_path);

        if ($response->status() > 399) {
            throw new Exception('Response status: ' . $response->status() . ' | Message: ' . json_encode($response->json(), JSON_UNESCAPED_UNICODE) . $response->body());
        }
        return $response->json();
    }
}
