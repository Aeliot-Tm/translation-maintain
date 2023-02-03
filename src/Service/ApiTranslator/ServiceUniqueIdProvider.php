<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

final class ServiceUniqueIdProvider
{
    /**
     * @var array<string,string|null>
     */
    private array $keys;

    /**
     * @param array<string,string|null> $keys
     */
    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function getId(string $serviceId): string
    {
        if (!$key = $this->keys[$serviceId]) {
            throw new \DomainException(sprintf('Undefined key for service "%s"', $serviceId));
        }

        // TODO use more secure algorithm
        return md5(sprintf('%s.%s', $serviceId, $key));
    }
}
