<?php

namespace Luttje\ExampleTester\Parser;

class MarkerConfig
{
    protected string $symbol;
    protected string $startMarker;
    protected string $endMarker;
    protected bool $short = true;

    public static function fromArray(array $array): static
    {
        $markerConfig = new static();

        foreach($array as $key => $value) {
            if (!property_exists(static::class, $key)) {
                throw new \Exception(sprintf('Property "%s" does not exist on %s', $key, static::class));
            }

            $markerConfig->$key = $value;
        }

        return $markerConfig;
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
