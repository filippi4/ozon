<?php

namespace Filippi4\Ozon\Exceptions;


use Psr\Http\Message\ResponseInterface;
use Throwable;

class OzonHttpException extends OzonException
{
    protected ?ResponseInterface $response = null;

    public function __construct(
        mixed $message = "",
        int $code = 0,
        ?Throwable $previous = null,
        ?ResponseInterface $response = null
    ) {
        if ($message instanceof Throwable) {
            $previous = $message;
            $code = (int) $message->getCode();
            $message = $message->getMessage();
        }
        parent::__construct((string) $message, (int) $code, $previous);
        $this->response = $response;
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }

    public function getRetryAfter(): ?int
    {
        if ($this->response && $this->response->hasHeader('Retry-After')) {
            $retryAfter = trim($this->response->getHeaderLine('Retry-After'));
            if (is_numeric($retryAfter)) {
                return (int) $retryAfter;
            }
            $timestamp = strtotime($retryAfter);
            if ($timestamp !== false) {
                return max(1, $timestamp - time());
            }
        }

        return null;
    }

    public function getRatelimitRemaining(): ?int
    {
        if ($this->response && $this->response->hasHeader('Ratelimit-Remaining')) {
            $remaining = trim($this->response->getHeaderLine('Ratelimit-Remaining'));
            return is_numeric($remaining) ? (int) $remaining : null;
        }

        return null;
    }
}
