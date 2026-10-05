<?php

namespace Filippi4\Ozon;

class OzonData
{
    public $data;
    public $status;
    public ?int $ratelimitRemaining = null;
    public ?OzonResponse $response = null;

    public function __construct(OzonResponse $response)
    {
        $this->response = $response;
        $data = $response->toSimpleObject();
        $this->data = $data->data;
        $this->status = $data->status;
        $this->ratelimitRemaining = $response->getRatelimitRemaining();
    }
}
