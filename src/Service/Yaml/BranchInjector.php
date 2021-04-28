<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Exception\KeyCollisionException;

final class BranchInjector
{
    public function inject(array $yaml, array $branch): array
    {
        // NOTE: this is initial simple implementation of injector
        // TODO: Use KeysTransformer approach for the merging of branches
        foreach ($branch as $key => $value) {
            if (isset($yaml[$key])) {
                if (is_array($yaml[$key])) {
                    if (is_array($value)) {
                        $yaml[$key] = $this->inject($yaml[$key], $value);
                    } else {
                        throw new KeyCollisionException('Cannot merge different types');
                    }
                } elseif ($yaml[$key] !== $value) {
                    throw new KeyCollisionException('Not same values');
                }
            } else {
                $yaml[$key] = $value;
            }
        }

        return $yaml;
    }
}
