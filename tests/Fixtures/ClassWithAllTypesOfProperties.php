<?php

namespace Luttje\ExampleTester\Tests\Fixtures;

final class ClassWithAllTypesOfProperties
{
    protected array $array;
    protected bool $bool;
    protected \Closure $closure;
    protected float $float;
    protected int $int;
    protected object $object;
    protected string $string;
    protected string|bool $stringOrBool;
    protected int|null $intOrNull;

    public function __construct()
    {
        $this->array = [];
        $this->bool = true;
        $this->closure = fn () => null;
        $this->float = 1.0;
        $this->int = 1;
        $this->object = new \stdClass();
        $this->string = 'string';
        $this->stringOrBool = 'string';
        $this->intOrNull = null;
    }
}
