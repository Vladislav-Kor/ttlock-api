<?php

declare(strict_types=1);

namespace app\repositories;

class Hydrator
{
    private $reflectionClassMap;

    /**
     * hydrate.
     *
     * @param object|string $class
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public function hydrate($class, array $data)
    {
        $reflection = $this->getReflectionClass($class);
        $target = $reflection->newInstanceWithoutConstructor();
        foreach ($data as $name => $value) {
            $property = $reflection->getProperty($name);
            if ($property->isPrivate() || $property->isProtected()) {
                $property->setAccessible(true);
            }
            $property->setValue($target, $value);
        }

        return $target;
    }

    /**
     * getReflectionClass.
     *
     * @param object|string $className
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    private function getReflectionClass($className)
    {
        if (!isset($this->reflectionClassMap[$className])) {
            $this->reflectionClassMap[$className] = new \ReflectionClass($className);
        }

        return $this->reflectionClassMap[$className];
    }
}
