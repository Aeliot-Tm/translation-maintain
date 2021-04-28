<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Model\Layer;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyParserTrait;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyValidationTrait;

final class KeysTransformer implements TransformerInterface
{
    use KeyParserTrait;
    use KeyValidationTrait;

    public function transform(array $yaml): array
    {
        $layer = new Layer();
        $layer->setYaml($yaml);
        $this->iterate($layer);

        return $layer->getYaml();
    }

    private function iterate(Layer $current): void
    {
        $yaml = &$current->getYaml();
        foreach (array_keys($yaml) as $key) {
            if (is_array($yaml[$key])) {
                $yaml[$key] = $this->transform($yaml[$key]);
            }
            if ($this->isSplittable($key)) {
                $nPoint = $this->createNestedValue(explode('.', $key), $yaml[$key]);
                $nKey = $this->getFirstKey($nPoint);
                if (isset($yaml[$nKey])) {
                    //NOTE: nothing to do if $nPoint[$nKey] is not an array. We should not explode such keys
                    if (is_array($yaml[$nKey])) {
                        //NOTE: $nPoint[$nKey] is always an array there
                        $layer = $this->createChildLayer($current, $nKey, $yaml[$nKey], $nPoint[$nKey]);
                        $this->work($layer);
                        if (!$layer->getSelectedNPoint()) {
                            unset($yaml[$key]);
                        }
                    }
                } else {
                    $yaml[$nKey] = $nPoint[$nKey];
                    unset($yaml[$key]);
                }
            }
        }
    }

    private function work(Layer $current): void
    {
        if ($current->isUp()) {
            $yPoint = &$current->getSelectedYPoint();
            if ($nPoint = &$current->getSelectedNPoint()) {
                $nKey = $this->getFirstKey($nPoint);
                if ($current->hasWayUp()) {
                    if (isset($yPoint[$nKey])) {
                        if (is_array($yPoint[$nKey])) {
                            if (is_array($nPoint[$nKey])) {
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
                            if (is_array($nPoint[$nKey])) {
                                $layer = $this->createChildLayer($current, $nKey, $yPoint, $nPoint[$nKey]);
                                $layer->setNoWayUp();
                                $this->work($layer);
                            } elseif ($yPoint[$nKey] !== $nPoint[$nKey]) {
                                //NOTE: nothing to do if values the save
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
                        //NOTE: $nPoint[$nKey] is always an array
                        $layer = $this->createChildLayer($current, $nKey, $yPoint[$resultKey], $nPoint[$nKey]);
                        $layer->setHasWayUp();
                        $this->work($layer);
                    } else {
                        $yPoint[$resultKey] = $nPoint[$nKey];
                        unset($nPoint[$nKey]);
                        $this->work($current);
                    }
                }
            } elseif (($parent = $current->getParent()) && $parent->getParent()) {
                $parent->goUp();
                $parent->setHasWayUp();
                $nPoint = &$parent->getSelectedNPoint();
                unset($nPoint[$current->getSelectedKey()]);
                $this->work($parent);
            }
        } elseif ($parent = $current->getParent()) {
            $nKey = $current->getSelectedKey();
            $pnPoint = &$parent->getSelectedNPoint();
            if ($current->isSameValue()) {
                $pKey = $parent->getSelectedKey();
                $resultKey = $pKey.'.'.$nKey;
                unset($pnPoint[$pKey]);
                $pnPoint[$resultKey] = $current->getSelectedNPoint();
            } else {
                $cnPoint = &$current->getSelectedNPoint();
                $fKey = $this->getFirstKey($cnPoint);
                $resultKey = $nKey.'.'.$fKey;
                $pnPoint[$resultKey] = $cnPoint[$fKey];
                unset($pnPoint[$nKey]);
            }
            $parent->setSelectedKey($resultKey);
            $this->work($parent);
        }
    }

    private function createChildLayer(Layer $parent, string $key, array &$yPoint, array &$nPoint): Layer
    {
        $layer = new Layer($parent);
        $layer->setSelectedKey($key);
        $layer->setSelectedYPoint($yPoint);
        $layer->setSelectedNPoint($nPoint);

        return $layer;
    }

    private function getFirstKey(array $branch): string
    {
        $keys = array_keys($branch);

        return reset($keys);
    }
}
