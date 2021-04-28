<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Exception\KeyCollisionException;
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

    private array $dirs;
    private BranchInjector $branchInjector;
    private FileManipulator $fileManipulator;
    private LoggerInterface $logger;
    private string $position;

    public function __construct(
        iterable $dirs,
        string $position,
        BranchInjector $branchInjector,
        FileManipulator $fileManipulator,
        ?LoggerInterface $logger = null
    ) {
        $this->dirs = $dirs instanceof \Traversable ? iterator_to_array($dirs) : (array) $dirs;
        $this->branchInjector = $branchInjector;
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
        foreach ($this->dirs as $dir) {
            foreach (['yaml', 'yml'] as $extension) {
                if ($this->fileManipulator->exists($filePath = $dir.'/'.$domain.'.'.$locale.'.'.$extension)) {
                    return $filePath;
                }
            }
        }

        return reset($this->dirs).'/'.$domain.'.'.$locale.'.yaml';
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

    /**
     * @param array $yaml
     * @param string $id
     * @return array
     */
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
