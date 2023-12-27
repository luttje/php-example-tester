<?php

namespace Luttje\ExampleTester\Parser;

class MarkerConfig
{
    public function __construct(
        protected string $method,
        protected string $startMarker,
        protected string $endMarker,
    )
    { }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getStartMarker(): string
    {
        return $this->startMarker;
    }

    public function getEndMarker(): string
    {
        return $this->endMarker;
    }
}
