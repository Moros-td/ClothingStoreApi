<?php

namespace Firebase\JWT;

class ExpiredException extends \UnexpectedValueException implements JWTExceptionWithPayloadInterface
{
    private $payload;

    public function setPayload(object $payload): void
    {
        $this->payload = $payload;
    }

    public function getPayload(): object
    {
        return $this->payload;
    }
}
