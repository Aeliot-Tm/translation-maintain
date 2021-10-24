<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

abstract class AbstractLine implements ReportLineInterface
{
    /**
     * @return string[]
     */
    public static function getHeaders(): array
    {
        $reflection = new \ReflectionClass(static::class);
        /** @var static $instance */
        $instance = $reflection->newInstanceWithoutConstructor();

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            if (!$property->isInitialized($instance)) {
                $defaultValue = null;
                if ($type = $property->getType()) {
                    switch ($type->getName()) {
                        case 'string':
                            $defaultValue = '';
                            break;
                        case 'array':
                            $defaultValue = [];
                            break;
                    }
                }
                $property->setValue($instance, $defaultValue);
            }
        }

        return array_keys($instance->getNamedValues());
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        $values = [];
        foreach ($this->getNamedValues() as $key => $value) {
            if (\is_array($value)) {
                sort($value);
                $value = implode(', ', $value);
            }

            $values[$key] = $value;
        }

        return $values;
    }

    /**
     * @return array<string,mixed>
     */
    abstract protected function getNamedValues(): array;
}
