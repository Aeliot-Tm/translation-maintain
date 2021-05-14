<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Service\Yaml\MissedValuesFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class ExportMissedTranslationsCommand extends Command
{
    private MissedValuesFinder $missedValuesFinder;

    public function __construct(MissedValuesFinder $missedValuesFinder)
    {
        parent::__construct('aeliot_trans_maintain:yaml:export_missed_translations');

        $this->missedValuesFinder = $missedValuesFinder;
    }

    protected function configure(): void
    {
        $this->setDescription('Export missed translations in YAML files');
        $this->addArgument('domain', InputArgument::REQUIRED, 'Domain name');
        $this->addArgument('source_locale', InputArgument::REQUIRED, 'Locale code from data will be taken');
        $this->addArgument('target_locale', InputArgument::OPTIONAL, 'Locale code exported for. Used for filterring if passed');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $domain = $input->getArgument('domain');
        $sourceLocale = $input->getArgument('source_locale');
        $targetLocale = $input->hasArgument('target_locale') ? $input->getArgument('target_locale') : null;

        $values = $this->missedValuesFinder->findMissedTranslations($domain, $sourceLocale, $targetLocale);
        $output->writeln(Yaml::dump($values));

        return 0;
    }
}
