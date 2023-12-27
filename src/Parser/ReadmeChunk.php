<?php

namespace Luttje\ExampleTester\Parser;

class ReadmeChunk
{
    public function __construct(
        protected string $content
    )
    { }

    public function getContent(): string
    {
        return $this->content;
    }
}
