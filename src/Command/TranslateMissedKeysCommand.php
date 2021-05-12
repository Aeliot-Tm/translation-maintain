<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Mediator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\BranchInjector;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\MissedValuesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\TransformationConveyor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TranslateMissedKeysCommand extends Command
{
    private BranchInjector $branchInjector;
    private FilesFinder $filesFinder;
    private FileManipulator $fileManipulator;
    private MissedValuesFinder $missedValuesFinder;
    private Mediator $translatorMediator;
    private TransformationConveyor $transformationConveyor;

    public function __construct(
        BranchInjector $branchInjector,
        FilesFinder $filesFinder,
        FileManipulator $fileManipulator,
        MissedValuesFinder $missedValuesFinder,
        TransformationConveyor $transformationConveyor,
        Mediator $translatorMediator
    ) {
        parent::__construct('aeliot_trans_maintain:yaml:translate');

        $this->branchInjector = $branchInjector;
        $this->filesFinder = $filesFinder;
        $this->fileManipulator = $fileManipulator;
        $this->missedValuesFinder = $missedValuesFinder;
        $this->transformationConveyor = $transformationConveyor;
        $this->translatorMediator = $translatorMediator;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for translation missed data');

        $this->addOption('domain', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Domain name');
        $this->addOption(
            'source_locale',
            's',
            InputOption::VALUE_REQUIRED,
            'Locale code where data will be taken',
            'en'
        );
        $this->addOption(
            'target_locale',
            't',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Locale code where are missed data'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceLocale = $input->getOption('source_locale');
        $targetLocales = $this->getTargetLocales($input);

        foreach ($this->getDomains($input) as $domain) {
            foreach ($targetLocales as $targetLocale) {
                $values = $this->missedValuesFinder->findMissedTranslations($domain, $sourceLocale, $targetLocale);
                if (!$values) {
                    continue;
                }
                $values = $this->translatorMediator->translateBatch($values, $targetLocale, $sourceLocale);

                $this->save($domain, $targetLocale, $values);
            }
        }

        return 0;
    }

    /**
     * @return string[]
     */
    private function getDomains(InputInterface $input): array
    {
        $requestedDomains = $input->getOption('domain') ?: [];
        $existingDomains = $this->filesFinder->getDomains();

        return $requestedDomains ? array_intersect($existingDomains, $requestedDomains) : $existingDomains;
    }

    /**
     * @return string[]
     */
    private function getTargetLocales(InputInterface $input): array
    {
        $requestedLocales = $input->getOption('target_locale') ?: [];
        $existingLocales = $this->filesFinder->getLocales();

        return $requestedLocales ? array_intersect($existingLocales, $requestedLocales) : $existingLocales;
    }

    private function merge(array $yaml, array $values): array
    {
        foreach ($values as $key => $value) {
            if (!$this->branchInjector->inject($yaml, $key, $value)) {
                throw new \DomainException(\sprintf('Cannot inject key %s', $key));
            }
        }

        return $yaml;
    }

    private function save(string $domain, string $targetLocale, array $values): void
    {
        $path = $this->filesFinder->locateFile($domain, $targetLocale);
        if ($this->fileManipulator->exists($path)) {
            $values = $this->merge($this->fileManipulator->parse($path), $values);
        }
        $values = $this->transformationConveyor->transform($values);
        $this->fileManipulator->dump($path, $values);
    }
}
