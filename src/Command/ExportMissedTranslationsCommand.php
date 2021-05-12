<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class ExportMissedTranslationsCommand extends Command
{
    private FilesFinder $filesFinder;
    private KeysParser $keysParser;

    public function __construct(FilesFinder $filesFinder, KeysParser $keysParser)
    {
        parent::__construct('aeliot_trans_maintain:yaml:export_missed_translations');

        $this->filesFinder = $filesFinder;
        $this->keysParser = $keysParser;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the sorting of yaml files');
        $this->addArgument('domain', InputArgument::REQUIRED, 'Domain name');
        $this->addArgument('locale_from', InputArgument::REQUIRED, 'Locale code from data will be taken');
        $this->addArgument('locale_for', InputArgument::OPTIONAL, 'Locale code exported for. Used for filterring if passed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domain = $input->getArgument('domain');
        $localeFrom = $input->getArgument('locale_from');
        $domainsFiles = $this->filesFinder->getFilesMap();

        if (!isset($domainsFiles[$domain][$localeFrom])) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid domain "%s" or locale "%s" posted', $domain, $localeFrom)
            );
        }

        $parsedKeys = $this->keysParser->getParsedKeys($domainsFiles[$domain]);
        $omittedKeys = $this->keysParser->getOmittedKeys($parsedKeys);
        $allOmittedKeys = $this->keysParser->mergeKeys($omittedKeys);

        $values = array_intersect_key(
            $this->keysParser->parseFiles($domainsFiles[$domain][$localeFrom]),
            array_flip($allOmittedKeys)
        );
        if ($input->hasArgument('locale_for')) {
            if (!$filterKeys = $omittedKeys[$localeFor = $input->getArgument('locale_for')] ?? null) {
                throw new \InvalidArgumentException(\sprintf('There is no omitted keys for locale "%s"', $localeFor));
            }

            $values = array_intersect_key($values, array_flip($filterKeys));
        }

        ksort($values);

        $output->writeln(Yaml::dump($values));

        return 0;
    }
}
