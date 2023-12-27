<?php

namespace Luttje\ExampleTester\Tests\Unit;

use Luttje\ExampleTester\Parser\MarkerConfig;
use Luttje\ExampleTester\Tests\TestCase;

/**
 * @group marker-config
 */
final class MarkerConfigTest extends TestCase
{
    public function testFromArray()
    {
        $markerConfig = MarkerConfig::fromArray([
            'symbol' => 'symbol',
            'startMarker' => 'startMarker',
            'endMarker' => 'endMarker',
            'short' => false,
            'prefix' => 'prefix',
        ]);

        $this->assertEquals('symbol', $markerConfig->getSymbol());
        $this->assertEquals('startMarker', $markerConfig->getStartMarker());
        $this->assertEquals('endMarker', $markerConfig->getEndMarker());
        $this->assertEquals(false, $markerConfig->isShort());
        $this->assertEquals('prefix', $markerConfig->getPrefix());
    }

    public function testFromArrayWithInvalidProperty()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "invalidProperty" does not exist on Luttje\ExampleTester\Parser\MarkerConfig');

        MarkerConfig::fromArray([
            'invalidProperty' => 'invalidProperty',
        ]);
    }

    public function testFromArrayWithInvalidPropertyTypeSymbol()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "symbol" must be of type "string"');

        MarkerConfig::fromArray([
            'symbol' => 123,
        ]);
    }

    public function testFromArrayWithInvalidPropertyTypeStartMarker()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "startMarker" must be of type "string"');

        MarkerConfig::fromArray([
            'startMarker' => 123,
        ]);
    }

    public function testFromArrayWithInvalidPropertyTypeEndMarker()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "endMarker" must be of type "string"');

        MarkerConfig::fromArray([
            'endMarker' => 123,
        ]);
    }

    public function testFromArrayWithInvalidPropertyTypeShort()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "short" must be of type "bool"');

        MarkerConfig::fromArray([
            'short' => 123,
        ]);
    }

    public function testFromArrayWithInvalidPropertyTypePrefix()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "prefix" must be of type "string"');

        MarkerConfig::fromArray([
            'prefix' => 123,
        ]);
    }
}
