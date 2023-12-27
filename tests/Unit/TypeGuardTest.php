<?php

namespace Luttje\ExampleTester\Tests\Unit;

use Luttje\ExampleTester\Helpers\TypeGuard;
use Luttje\ExampleTester\Tests\Fixtures\ClassWithAllTypesOfProperties;
use Luttje\ExampleTester\Tests\TestCase;

/**
 * @group type-guard
 */
final class TypeGuardTest extends TestCase
{
    public function testThrowIfTypeIsMissingOrWrong()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "unknown" does not exist on Luttje\ExampleTester\Tests\Fixtures\ClassWithAllTypesOfProperties');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'unknown', 'unknown');
    }

    public function testThrowIfTypeIsMissingOrWrongWithArray()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "array" must be of type "array"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'array', 'array');
    }

    public function testThrowIfTypeIsMissingOrWrongWithBool()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "bool" must be of type "bool"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'bool', 'bool');
    }

    public function testThrowIfTypeIsMissingOrWrongWithClosure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "closure" must be of type "Closure"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'closure', 'closure');
    }

    public function testThrowIfTypeIsMissingOrWrongWithFloat()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "float" must be of type "float"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'float', 'float');
    }

    public function testThrowIfTypeIsMissingOrWrongWithInt()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "int" must be of type "int"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'int', 'int');
    }

    public function testThrowIfTypeIsMissingOrWrongWithObject()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "object" must be of type "object"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'object', 'object');
    }

    public function testThrowIfTypeIsMissingOrWrongWithString()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "string" must be of type "string"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'string', NAN);
    }

    public function testThrowIfTypeIsMissingOrWrongWithStringOrBool()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "stringOrBool" must be of type "string"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'stringOrBool', 123);
    }

    public function testCanHandleUnionTypeStringOrBool()
    {
        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'stringOrBool', 'string');
        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'stringOrBool', true);

        $this->assertTrue(true, 'TypeGuard should not throw an exception');
    }

    public function testThrowIfTypeIsMissingOrWrongWithIntOrNull()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Property "intOrNull" must be of type "int"');

        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'intOrNull', 'string');
    }

    public function testCanHandleUnionTypeIntOrNull()
    {
        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'intOrNull', 123);
        TypeGuard::throwIfTypeIsMissingOrWrong(ClassWithAllTypesOfProperties::class, 'intOrNull', null);

        $this->assertTrue(true, 'TypeGuard should not throw an exception');
    }
}
