<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\Catalogue\MergeOperation;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\LoggingTranslator;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

final class DetectMissedTranslationsCommand extends Command
{
    private const MESSAGE_MISSING = 0;
    private const MESSAGE_UNUSED = 1;
    private const MESSAGE_EQUALS_FALLBACK = 2;

    private ?string $defaultTransPath;
    private ?string $defaultViewsPath;
    private ExtractorInterface $extractor;
    private TranslationReaderInterface $reader;
    private TranslatorInterface $translator;
    private array $transPaths;
    private array $viewsPaths;

    public function __construct(
        TranslatorInterface $translator,
        TranslationReaderInterface $reader,
        ExtractorInterface $extractor,
        string $defaultTransPath = null,
        string $defaultViewsPath = null,
        array $transPaths = []
        //, DirectoryProvider $directoryProvider = null
    ) {
        parent::__construct('aeliot_trans_maintain:missed:detect');

        $this->extractor = $extractor;
        $this->translator = $translator;
        $this->reader = $reader;
        $this->defaultTransPath = $defaultTransPath;
        $this->defaultViewsPath = $defaultViewsPath;
        $this->transPaths = $transPaths;
    }

    protected function configure(): void
    {
        $this->setDescription('Command extracts used translations and detect missed');
        $this->addArgument('locale', InputArgument::REQUIRED, 'The locale');
        $this->addArgument('bundle', InputArgument::OPTIONAL, 'The bundle name or directory where to load the messages');
        $this->addOption('domain', null, InputOption::VALUE_OPTIONAL, 'The messages domain');
        $this->addOption('view', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'View paths', []);
        $this->addOption('only-missing', null, InputOption::VALUE_NONE, 'Displays only missing messages');
        $this->addOption('only-unused', null, InputOption::VALUE_NONE, 'Displays only unused messages');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Load messages from all registered bundles');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $locale = $input->getArgument('locale');
        $domain = $input->getOption('domain');
        $viewsPaths = $this->getViewPaths($input);
        $transPaths = $this->getTransPaths();

        // Load defined messages
        $currentCatalogue = $this->loadCurrentMessages($locale, $transPaths);

        // Extract used messages
        $extractedCatalogue = $this->extractMessages($locale, $viewsPaths);

        // Merge defined and extracted messages to get all message ids
        $allMessages = $this->getAllMessages($extractedCatalogue, $currentCatalogue, $domain);

        // No defined or extracted messages
        if (empty($allMessages) || (null !== $domain && empty($allMessages[$domain]))) {
            $io->getErrorStyle()->warning($this->createNoMessagesNotification($locale, $domain));

            return 0;
        }

        // Display header line
        $headers = ['State', 'Domain', 'Id', sprintf('Message Preview (%s)', $locale)];
        $rows = $this->getTableRows($allMessages, $extractedCatalogue, $currentCatalogue);

        $io->table($headers, $rows);

        return 0;
    }

    private function formatId(string $id): string
    {
        return sprintf('<fg=cyan;options=bold>%s</>', $id);
    }

    private function sanitizeString(string $string, int $length = 40): string
    {
        $string = trim(preg_replace('/\s+/', ' ', $string));

        if (false !== $encoding = mb_detect_encoding($string, null, true)) {
            if (mb_strlen($string, $encoding) > $length) {
                return mb_substr($string, 0, $length - 3, $encoding).'...';
            }
        } elseif (\strlen($string) > $length) {
            return substr($string, 0, $length - 3).'...';
        }

        return $string;
    }

    private function extractMessages(string $locale, array $transPaths): MessageCatalogue
    {
        $extractedCatalogue = new MessageCatalogue($locale);
        foreach ($transPaths as $path) {
            if (is_dir($path) || is_file($path)) {
                $this->extractor->extract($path, $extractedCatalogue);
            }
        }

        return $extractedCatalogue;
    }

    private function loadCurrentMessages(string $locale, array $transPaths): MessageCatalogue
    {
        $currentCatalogue = new MessageCatalogue($locale);
        foreach ($transPaths as $path) {
            if (is_dir($path)) {
                $this->reader->read($path, $currentCatalogue);
            }
        }

        return $currentCatalogue;
    }

    /**
     * @return MessageCatalogue[]
     */
    private function loadFallbackCatalogues(string $locale, array $transPaths): array
    {
        $fallbackCatalogues = [];
        if ($this->translator instanceof Translator || $this->translator instanceof DataCollectorTranslator || $this->translator instanceof LoggingTranslator) {
            foreach ($this->translator->getFallbackLocales() as $fallbackLocale) {
                if ($fallbackLocale === $locale) {
                    continue;
                }

                $fallbackCatalogue = new MessageCatalogue($fallbackLocale);
                foreach ($transPaths as $path) {
                    if (is_dir($path)) {
                        $this->reader->read($path, $fallbackCatalogue);
                    }
                }
                $fallbackCatalogues[] = $fallbackCatalogue;
            }
        }

        return $fallbackCatalogues;
    }

    private function createNoMessagesNotification($locale, $domain): string
    {
        $outputMessage = sprintf('No defined or extracted messages for locale "%s"', $locale);

        if (null !== $domain) {
            $outputMessage .= sprintf(' and domain "%s"', $domain);
        }

        return $outputMessage;
    }

    private function getViewPaths(InputInterface $input): array
    {
        $viewsPaths = (array) ($input->getOption('view') ?: []);
        if (!$viewsPaths && $this->defaultViewsPath) {
            $viewsPaths[] = $this->defaultViewsPath;
        }

        return $viewsPaths;
    }

    private function getTransPaths(): array
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->getApplication()->getKernel();

        // Define Root Paths
        $transPaths = $this->transPaths;
        if ($this->defaultTransPath) {
            $transPaths[] = $this->defaultTransPath;
        }

        // Override with provided Bundle info
        foreach ($kernel->getBundles() as $bundle) {
            $bundleDir = $bundle->getPath();

            $transPaths[] = is_dir($bundleDir.'/Resources/translations')
                ? $bundleDir.'/Resources/translations'
                : $bundleDir.'/translations';
        }

        return $transPaths;
    }

    /**
     * @return array<string,mixed>
     */
    private function getAllMessages(
        MessageCatalogue $extractedCatalogue,
        MessageCatalogue $currentCatalogue,
        ?string $domain
    ): array {
        $mergeOperation = new MergeOperation($extractedCatalogue, $currentCatalogue);
        $allMessages = $mergeOperation->getResult()->all($domain);
        if (null !== $domain) {
            $allMessages = [$domain => $allMessages];
        }

        return $allMessages;
    }

    /**
     * @param array<string,string> $allMessages
     *
     * @return string[]
     */
    private function getTableRows(
        array $allMessages,
        MessageCatalogue $extractedCatalogue,
        MessageCatalogue $currentCatalogue
    ): array {
        $rows = [];
        // Iterate all message ids and determine their state
        foreach ($allMessages as $domain => $messages) {
            foreach (array_keys($messages) as $messageId) {
                if (!$extractedCatalogue->defines($messageId, $domain)
                    || $currentCatalogue->defines($messageId, $domain)
                ) {
                    continue;
                }

                $value = $currentCatalogue->get($messageId, $domain);
                $rows[] = [$domain, $this->formatId($messageId), $this->sanitizeString($value)];
            }
        }

        return $rows;
    }
}
