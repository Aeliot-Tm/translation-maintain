<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Exception\KeyCollisionException;
use Aeliot\Bundle\TransMaintain\Service\DirectoryProvider;
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

    private array $dirs;
    private BranchInjector $branchInjector;
    private FileManipulator $fileManipulator;
    private LoggerInterface $logger;
    private string $position;
    private DirectoryProvider $directoryProvider;

    public function __construct(
        string $position,
        BranchInjector $branchInjector,
        DirectoryProvider $directoryProvider,
        FileManipulator $fileManipulator,
        ?LoggerInterface $logger = null
    ) {
        $this->branchInjector = $branchInjector;
        $this->directoryProvider = $directoryProvider;
        $this->fileManipulator = $fileManipulator;
        $this->logger = $logger ?? new NullLogger();
        $this->position = $position;
    }

    public function register(string $id, string $domain, string $locale): void
    {
        $path = $this->locatePath($domain, $locale);
        $yaml = $this->fileManipulator->exists($path) ? $this->fileManipulator->parse($path) : [];

        try {
            $yaml = $this->inject($yaml, $id);
            $this->fileManipulator->dump($path, $yaml);
        } catch (KeyCollisionException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }
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

    /**
     * @return string[]
     */
    private function createBranch(string $id): array
    {
        if ($this->isSplittable($id)) {
            return $this->createNestedValue(explode('.', $id), $id);
        }

        return [$id => $id];
    }

    private function inject(array $yaml, string $id): array
    {
        switch ($this->position) {
            case self::MERGE:
                $yaml = $this->branchInjector->inject($yaml, $this->createBranch($id));
                break;
            case self::TO_THE_END:
                if (isset($yaml[$id]) && $yaml[$id] !== $id) {
                    throw new KeyCollisionException('Key exists');
                }
                $yaml[$id] = $id;
                break;
            case self::NO:
            default:
                throw new \LogicException(\sprintf('Invalid position: "%s"', $this->position));
        }

        return $yaml;
    }
}
