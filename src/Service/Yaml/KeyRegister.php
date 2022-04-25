<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Exception\KeyCollisionException;
use Aeliot\Bundle\TransMaintain\Service\DirectoryProvider;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Inserter\InserterInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class KeyRegister
{
    use KeyParserTrait;
    use KeyValidationTrait;

    public const NO = 'no';
    public const MERGE = 'merge';
    public const TO_THE_END = 'end';

    public const POSITIONS = [self::NO, self::MERGE, self::TO_THE_END];

    private const EXTENSIONS = ['yaml', 'yml'];

    private DirectoryProvider $directoryProvider;
    private FileManipulator $fileManipulator;
    /**
     * @var InserterInterface[]
     */
    private array $inserters = [];
    private LoggerInterface $logger;
    private string $position;

    public function __construct(
        iterable $inserters,
        string $position,
        DirectoryProvider $directoryProvider,
        FileManipulator $fileManipulator,
        ?LoggerInterface $logger = null
    ) {
        $this->directoryProvider = $directoryProvider;
        $this->fileManipulator = $fileManipulator;
        $this->logger = $logger ?? new NullLogger();
        $this->position = $position;

        foreach ($inserters as $inserter) {
            $this->addInserter($inserter);
        }
    }

    public function addInserter(InserterInterface $inserter): void
    {
        if (array_key_exists($position = $inserter->getPosition(), $this->inserters)) {
            throw new \LogicException(\sprintf('An inserter for the position "%s" has registered', $position));
        }

        $this->inserters[$position] = $inserter;
    }

    public function register(string $id, string $domain, string $locale): void
    {
        $path = $this->locatePath($domain, $locale);
        $yaml = $this->fileManipulator->exists($path) ? $this->fileManipulator->parse($path) : [];

        try {
            $yaml = $this->getInserter()->insert($yaml, $id, $id);
            $this->fileManipulator->dump($path, $yaml);
        } catch (KeyCollisionException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    private function getInserter(): InserterInterface
    {
        if (!array_key_exists($this->position, $this->inserters)) {
            throw new \LogicException(\sprintf('Invalid position: "%s"', $this->position));
        }

        return $this->inserters[$this->position];
    }

    private function locatePath(string $domain, string $locale): string
    {
        foreach ($this->directoryProvider->getAll() as $dir) {
            foreach (self::EXTENSIONS as $extension) {
                if ($this->fileManipulator->exists($filePath = $dir.'/'.$domain.'.'.$locale.'.'.$extension)) {
                    return $filePath;
                }
            }
        }

        return $this->directoryProvider->getDefault().'/'.$domain.'.'.$locale.'.'.self::EXTENSIONS[0];
    }
}
