<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = new PhpCsFixer\Finder()
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/preload.php',
        'config/reference.php',
        'public/index.php',
    ]);

return new PhpCsFixer\Config()
    ->setUnsupportedPhpVersionAllowed(true)
    ->setRules([
        '@PER-CS3x0' => true,
        '@PHP8x5Migration' => true,
        'align_multiline_comment' => true,
        'assign_null_coalescing_to_coalesce_equal' => false,
        'blank_line_before_statement' => ['statements' => ['return', 'switch', 'try', 'while']],
        'global_namespace_import' => ['import_classes' => true],
        'heredoc_indentation' => false,
        'method_chaining_indentation' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'operator_linebreak' => ['only_booleans' => true],
        'ordered_class_elements' => true,
        'ordered_imports' => true,
        'ordered_interfaces' => true,
        'phpdoc_order' => ['order' => ['param', 'return', 'throws']],
        'phpdoc_param_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_var_without_name' => true,
        'single_quote' => true,
        'header_comment' => [
            'comment_type' => 'PHPDoc',
            'location' => 'after_declare_strict',
            'header' => "@copyright Copyright (c) 2019 - 2026 Communitales GmbH (https://www.communitales.com/)\n\nFor the full copyright and license information, please view the LICENSE\nfile that was distributed with this source code.",
        ],

    ])
    ->setFinder($finder)
    ->setCacheFile('var/cache/.php-cs-fixer.cache')
    ->setParallelConfig(ParallelConfigFactory::detect());
