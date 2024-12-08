<?php

declare(strict_types=1);

use PhpCsFixer\Finder;

/** @var Finder $finder */
$finder = require __DIR__ . '/finder.php';

$rules = [
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
    // 'final_class' => true,
    'fopen_flags' => true,
    'header_comment' => [
        'header' => <<<'EOF'
            This file is part of the TransMaintain.

            (c) Anatoliy Melnikov <5785276@gmail.com>

            This source file is subject to the MIT license that is bundled
            with this source code in the file LICENSE.
            EOF,
    ],
    'is_null' => true,
    'method_chaining_indentation' => true,
    'no_superfluous_elseif' => true,
    'no_superfluous_phpdoc_tags' => true,
    'no_unreachable_default_argument_value' => true,
    'no_useless_else' => true,
    'phpdoc_align' => ['align' => 'left'],
    'set_type_to_cast' => true,
    'simplified_if_return' => true,
    'strict_param' => true,
];

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules($rules)
    ->setFinder($finder);
