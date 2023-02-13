<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Model\Layer;

final class BranchInjector
{
    use KeyParserTrait;
    use KeyValidationTrait;

    /**
     * @param array<string,mixed> $yaml
     * @param string|array<string,mixed> $value
     */
    public function inject(array &$yaml, string $key, $value): bool
    {
        if (!$this->isSplittable($key)) {
            if (isset($yaml[$key]) && $yaml[$key] !== $value) {
                return false;
            }

            $yaml[$key] = $value;

            return true;
        }

        $nPoint = $this->createNestedValue(explode('.', $key), $value);
        $nKey = $this->getFirstKey($nPoint);
        if (isset($yaml[$nKey])) {
            $layer = $this->createParentLayer($yaml, $yaml, $nPoint);
            $this->work($layer);
            if ($layer->getSelectedNPoint()) {
                return false;
            }
        } else {
            $yaml[$nKey] = $nPoint[$nKey];
        }

        return true;
    }

    /**
     * @param array<string,mixed> $yPoint
     * @param array<string,mixed> $nPoint
     */
    private function createChildLayer(Layer $parent, string $key, array &$yPoint, array &$nPoint): Layer
    {
        $layer = new Layer($parent);
        $layer->setSelectedKey($key);
        $layer->setSelectedYPoint($yPoint);
        $layer->setSelectedNPoint($nPoint);

        return $layer;
    }

    /**
     * @param array<string,mixed> $yaml
     * @param array<string,mixed> $yPoint
     * @param array<string,mixed> $nPoint
     */
    private function createParentLayer(array &$yaml, array &$yPoint, array &$nPoint): Layer
    {
        $layer = new Layer();
        $layer->setYaml($yaml);
        $layer->setSelectedYPoint($yPoint);
        $layer->setSelectedNPoint($nPoint);

        return $layer;
    }

    /**
     * @param array<string,mixed> $branch
     */
    private function getFirstKey(array $branch): string
    {
        if (!$branch) {
            throw new \LogicException('Empty branch passed');
        }
        $keys = array_keys($branch);

        return reset($keys);
    }

    private function work(Layer $current): void
    {
        if ($current->isUp()) {
            $yPoint = &$current->getSelectedYPoint();
            if ($nPoint = &$current->getSelectedNPoint()) {
                $nKey = $this->getFirstKey($nPoint);
                if ($current->hasWayUp()) {
                    if (isset($yPoint[$nKey])) {
                        if (\is_array($yPoint[$nKey])) {
                            if (\is_array($nPoint[$nKey])) {
                                $layer = $this->createChildLayer($current, $nKey, $yPoint[$nKey], $nPoint[$nKey]);
                                $this->work($layer);
                            } else {
                                foreach (array_keys($yPoint[$nKey]) as $key) {
                                    $resultKey = $nKey.'.'.$key;
                                    $yPoint[$resultKey] = $yPoint[$nKey][$key];
                                }
                                unset($yPoint[$nKey]);
                                $this->work($current);
                            }
                        } else {
                            if (\is_array($nPoint[$nKey])) {
                                $layer = $this->createChildLayer($current, $nKey, $yPoint, $nPoint[$nKey]);
                                $layer->setNoWayUp();
                                $this->work($layer);
                            } elseif ($yPoint[$nKey] !== $nPoint[$nKey]) {
                                // NOTE: nothing to do if values the save
                                $current->goDown();
                                $current->setNotSameValue();
                                $this->work($current);
                            }
                        }
                    } else {
                        $yPoint[$nKey] = $nPoint[$nKey];
                        unset($nPoint[$nKey]);
                        if ($parent = $current->getParent()) {
                            $this->work($parent);
                        }
                    }
                } else {
                    $resultKey = $current->getResultKey().'.'.$nKey;
                    if (isset($yPoint[$resultKey])) {
                        // NOTE: $nPoint[$nKey] is always an array
                        if (\is_array($yPoint[$resultKey])) {
                            $layer = $this->createChildLayer($current, $nKey, $yPoint[$resultKey], $nPoint[$nKey]);
                            $layer->setHasWayUp();
                            $this->work($layer);
                        } else {
                            if (\is_array($nPoint[$nKey])) {
                                $layer = $this->createChildLayer($current, $nKey, $yPoint, $nPoint[$nKey]);
                                $layer->setNoWayUp();
                                $this->work($layer);
                            } elseif ($yPoint[$nKey] !== $nPoint[$nKey]) {
                                // NOTE: nothing to do if values the save
                                $current->goDown();
                                $current->setNotSameValue();
                                $this->work($current);
                            }
                        }
                    } else {
                        $yPoint[$resultKey] = $nPoint[$nKey];
                        unset($nPoint[$nKey]);
                        $this->work($current);
                    }
                }
            } elseif ($parent = $current->getParent()) {
                $parent->goUp();
                $parent->setHasWayUp();
                $nPoint = &$parent->getSelectedNPoint();
                unset($nPoint[$current->getSelectedKey()]);
                $this->work($parent);
            }
        } elseif ($parent = $current->getParent()) {
            $nKey = $current->getSelectedKey();
            $pnPoint = &$parent->getSelectedNPoint();
            $cnPoint = &$current->getSelectedNPoint();
            $fKey = $this->getFirstKey($cnPoint);
            $resultKey = $nKey.'.'.$fKey;
            $pnPoint[$resultKey] = $cnPoint[$fKey];
            unset($pnPoint[$nKey]);
            $this->work($parent);
        }
    }
}
