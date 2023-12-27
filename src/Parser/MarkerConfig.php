<?php

namespace Luttje\ExampleTester\Parser;

use Luttje\ExampleTester\Helpers\TypeGuard;

class MarkerConfig
{
    protected string $symbol;
    protected string $startMarker;
    protected string $endMarker;
    protected bool $short = true;
    protected ?string $prefix = null;

    public static function fromArray(array $array): static
    {
        $markerConfig = new static();

        foreach($array as $key => $value) {
            TypeGuard::throwIfTypeIsMissingOrWrong(static::class, $key, $value);

            $markerConfig->$key = $value;
        }

        return $markerConfig;
    }

    public function getPrefix(): string|null
    {
        return $this->prefix;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getStartMarker(): string
    {
        return $this->startMarker;
    }

    public function getEndMarker(): string
    {
        return $this->endMarker;
    }

    public function isShort(): bool
    {
        return $this->short;
    }
}
