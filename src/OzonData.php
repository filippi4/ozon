<?php

namespace KFilippovk\Ozon;

class OzonData
{
    public $data;
    public $status;

    public function __construct(OzonResponse $response)
    {
        $data = $response->toSimpleObject();
        $this->data = $data->data;
        $this->status = $data->status;
    }
}
