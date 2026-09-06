<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2018 - 2026 Communitales GmbH (https://www.communitales.com/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        phpunitCodeQuality: true,
    )
    ->withPhpSets(php84: true)
    ->withComposerBased(phpunit: true)
    ->withImportNames()
    ->withCache('var/cache/rector');
