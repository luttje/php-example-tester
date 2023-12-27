<?php

namespace Luttje\ExampleTester\Helpers;

class TypeGuard
{
    public static function throwIfTypeIsMissingOrWrong(string $class, string $key, mixed $value): void
    {
        if (!property_exists($class, $key)) {
            throw new \Exception(sprintf('Property "%s" does not exist on %s', $key, $class));
        }

        $reflection = new \ReflectionProperty($class, $key);
        $type = $reflection->getType();

        if ($type === null) {
            return;
        }

        $isUnion = $type instanceof \ReflectionUnionType;
        $isIntersection = $type instanceof \ReflectionIntersectionType;

        if ($isIntersection) {
            throw new \Exception(sprintf('Property "%s" is of type "%s", which is not supported', $key, $type->getName()));
        }

        $typeNames = [];
        if ($isUnion || $isIntersection) {
            foreach ($type->getTypes() as $t) {
                $typeNames[] = $t->getName();
            }
        } else {
            $typeNames[] = $type->getName();

            if ($type->allowsNull()) {
                $typeNames[] = 'null';
            }
        }

        $invalidTypeNames = [];

        foreach ($typeNames as $typeName) {
            $checker = match ($typeName) {
                'array' => 'is_array',
                'bool' => 'is_bool',
                \Closure::class => fn ($value) => $value instanceof \Closure,
                'double' => 'is_double',
                'float' => 'is_float',
                'int' => 'is_int',
                'null' => 'is_null',
                'object' => 'is_object',
                'string' => 'is_string',
                default => fn ($value) => $value instanceof $typeName || is_subclass_of($value, $typeName),
            };

            if ($checker === null) {
                continue;
            }

            if (!$checker($value)) {
                $invalidTypeNames[] = $typeName;
            }
        }

        if ($isUnion && count($invalidTypeNames) === count($typeNames)) {
            throw new \Exception(sprintf('Property "%s" must be of type "%s"', $key, implode('" or "', $typeNames)));
        } else if ($isIntersection && count($invalidTypeNames) === 0) {
            throw new \Exception(sprintf('Property "%s" must be of type "%s"', $key, implode('" and "', $typeNames)));
        } else if (count($invalidTypeNames) === count($typeNames)) {
            throw new \Exception(sprintf('Property "%s" must be of type "%s"', $key, implode('" or "', $typeNames)));
        } else if (count($invalidTypeNames) === 0) {
            return;
        }
    }
}
