<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class ReportLine implements ReportLineInterface
{
    /**
     * @var array<string,string|array<string>>
     */
    private array $columns;

    /**
     * @param array<string,string|array<string>> $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        $values = [];
        foreach ($this->columns as $key => $value) {
            if (\is_array($value)) {
                sort($value);
                $value = implode(', ', $value);
            }

            $values[$key] = $value;
        }

        return $values;
    }
}
