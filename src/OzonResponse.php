<?php


namespace KFilippovk\Ozon;

use League\Csv\Reader;
use Psr\Http\Message\ResponseInterface;

class OzonResponse
{
    protected ResponseInterface $response;
    /**
     * @var false|string
     */
    private $output = [];


    public function __construct(ResponseInterface $response, $is_json = true)
    {
        $this->response = $response;
        if ($is_json) {
            $this->parseResponse();
        } else {
            $this->parseResponseCsv();
        }
    }

    /**
     * Parse response
     */
    private function parseResponse(): void
    {
        $status = $this->response ? $this->response->getStatusCode() : 500;
        $response = $this->response ? $this->response->getBody()->getContents() : null;

        $this->output = [
            'status' => $status,
            'data' => json_decode($response, true),
        ];
    }

    /**
     * Parse response in CSV format
     */
    private function parseResponseCsv(): void
    {
        $status = $this->response ? $this->response->getStatusCode() : 500;
        $response = $this->response ? $this->response->getBody()->getContents() : null;
        $csv = Reader::createFromString($response)
            ->setDelimiter(';')
            ->setHeaderOffset(0);

        $this->output = [
            'status' => $status,
            'data' => $csv->jsonSerialize(),
        ];
    }

    public function toSimpleObject()
    {
        return json_decode(json_encode($this->output), false);
    }

    public function json()
    {
        return json_encode($this->output);
    }

    public function toArray()
    {
        return $this->output;
    }
}
