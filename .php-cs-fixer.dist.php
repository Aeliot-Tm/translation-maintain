<?php

declare(strict_types=1);

use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in('src')
    ->in('tests')
    ->filter(static fn (SplFileInfo $x): bool => !preg_match('~src/DependencyInjection$~', $x->getPath()))
    ->append([
        '.php-cs-fixer.dist.php',
        '.php-cs-fixer-finder.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'class_definition' => [
            'single_item_single_line' => true,
        ],
        'concat_space' => [
            'spacing' => 'none',
        ],
        'declare_strict_types' => true,
        'final_class' => true,
        'fopen_flags' => true,
        'is_null' => true,
        'method_chaining_indentation' => true,
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'set_type_to_cast' => true,
        'simplified_if_return' => true,
        'strict_param' => true,
        'void_return' => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__.'/var/.php_cs.cache');
