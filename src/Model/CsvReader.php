<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class CsvReader implements \Iterator
{
    private string $path;
    /**
     * @var resource
     */
    private $handler;
    private ?\Generator $innerGenerator;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->innerGenerator = $this->read();
    }

    public function __destruct()
    {
        $this->stop();
    }

    public function current(): array
    {
        return $this->innerGenerator->current();
    }

    public function next(): void
    {
        $this->innerGenerator->next();
    }

    public function key(): int
    {
        return $this->innerGenerator->key();
    }

    public function valid(): bool
    {
        return $this->innerGenerator && $this->innerGenerator->valid();
    }

    public function rewind(): void
    {
        $this->innerGenerator = $this->read();
    }

    public function stop(): void
    {
        if ($this->handler) {
            fclose($this->handler);
        }
        $this->innerGenerator = null;
    }

    private function open(): void
    {
        if (($handler = fopen($this->path, 'rb')) === false) {
            throw new \RuntimeException(sprintf("Can't read file `%s`.", $this->path));
        }

        $this->handler = $handler;
    }

    private function read(): \Generator
    {
        $this->open();

        $headers = [];
        $rowNumber = 0;
        $titleRowNumber = 1;
        while (!feof($this->handler)) {
            if (!$rowData = fgetcsv($this->handler)) {
                continue;
            }
            ++$rowNumber;

            if ($rowNumber < $titleRowNumber) {
                continue;
            }

            if ($rowNumber === $titleRowNumber) {
                $headers = $rowData;
                continue;
            }

            yield array_combine($headers, $rowData);
        }
    }
}
