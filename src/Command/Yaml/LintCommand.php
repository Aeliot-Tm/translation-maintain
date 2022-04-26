<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command\Yaml;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\ReportBagConsoleRenderer;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class LintCommand extends Command
{
    private LinterRegistry $linterRegistry;
    private ReportBagConsoleRenderer $reportBagConsoleRenderer;
    private ?string $yamlKeyPattern;

    public function __construct(
        LinterRegistry $linterRegistry,
        ReportBagConsoleRenderer $reportBagConsoleRenderer,
        ?string $yamlKeyPattern
    ) {
        parent::__construct('aeliot_trans_maintain:yaml:lint');

        $this->linterRegistry = $linterRegistry;
        $this->reportBagConsoleRenderer = $reportBagConsoleRenderer;
        $this->yamlKeyPattern = $yamlKeyPattern;
    }

    public function getHelp(): string
    {
        $presets = implode(', ', $this->linterRegistry->getExistingPresets());
        $help = "There are available presets: $presets.\n";

        $lintersKeys = implode(', ', $this->linterRegistry->getRegisteredLintersKeys());
        $help .= "There are available linters: $lintersKeys.\n";

        if ($this->yamlKeyPattern) {
            $help .= "\nThere is configured pattern for the checking of translation keys: $this->yamlKeyPattern\n";
        } else {
            $help .= "\nKey pattern is not configured. You have not to use such linter.\n";
        }

        return $help;
    }

    protected function configure(): void
    {
        $this->setDescription('Check YAML files');
        $this->addArgument('linter', InputArgument::IS_ARRAY, 'List of linters', [LinterInterface::PRESET_BASE]);
        $this->addOption('domain', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter domains');
        $this->addOption('locale', 'l', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter locales');
        $this->setAliases(['aeliot_trans_maintain:lint:yaml']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hasProblems = false;
        $filterDto = $this->createFilterDto($input);
        foreach ($this->getLinters($input) as $linter) {
            /** @var LinterInterface $linter */
            $reportBag = $linter->lint($filterDto);
            $hasProblems = $hasProblems || !$reportBag->isEmpty();
            $this->reportBagConsoleRenderer->render($reportBag, $output);
        }

        return (int) $hasProblems;
    }

    private function createFilterDto(InputInterface $input): LintYamlFilterDto
    {
        $filterDto = new LintYamlFilterDto();
        if ($input->hasOption('domain')) {
            $filterDto->domains = $input->getOption('domain');
        }
        if ($input->hasOption('locale')) {
            $filterDto->locales = $input->getOption('locale');
        }

        return $filterDto;
    }

    /**
     * @return \Generator<LinterInterface>
     */
    private function getLinters(InputInterface $input): \Generator
    {
        $linters = (array) $input->getArgument('linter');
        if (\in_array(LinterInterface::PRESET_ALL, $linters, true)) {
            if (1 !== \count($linters)) {
                throw new \InvalidArgumentException('Preset "all" must be a single arguments');
            }

            yield from $this->linterRegistry->getPresetLinters(LinterInterface::PRESET_ALL);
        }

        $linters = $this->transformPresetsToLinterKeys($linters);

        if ($invalid = array_diff($linters, $this->linterRegistry->getRegisteredLintersKeys())) {
            throw new \InvalidArgumentException(sprintf('Requested not available linters: %s', implode(', ', $invalid)));
        }

        foreach ($linters as $linterKey) {
            yield $this->linterRegistry->getLinter($linterKey);
        }
    }

    /**
     * @param string[] $linters
     *
     * @return string[]
     */
    private function transformPresetsToLinterKeys(array $linters): array
    {
        if ($requestedPresets = array_intersect($this->linterRegistry->getExistingPresets(), $linters)) {
            $linters = array_diff($linters, $requestedPresets);
            $presetsLinters = array_map(
                fn (string $preset): array => $this->linterRegistry->getPresetLintersKeys($preset),
                $requestedPresets
            );
            $linters = array_unique(array_merge($linters, ...array_values($presetsLinters)));
        }

        return $linters;
    }
}
