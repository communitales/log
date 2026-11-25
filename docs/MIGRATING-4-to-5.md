# Migrating from 4.x to 5.x

Applications upgrading directly from version 3 should first follow the [3.x to 4.x guide](MIGRATING-3-to-4.md).
Version 5 retains the PHP 8.4 and `psr/log` 3.x requirements from version 4. Once version 5 is available, update the
package constraint:

```sh
composer require communitales/log:^5.0
```

## Update exception calls

The positional argument order changed from `logException($exception, $level, $context)` to
`logException($exception, $context, $level)`:

```php
// Version 4
$logger->logException($exception, LogLevel::WARNING, ['source' => 'csv']);

// Version 5
$logger->logException($exception, ['source' => 'csv'], LogLevel::WARNING);
```

Calls with only the exception retain the default `error` level. Named arguments `level` and `context` do not need to
be reordered.

## Configure Sentry explicitly

Version 4 called `Sentry\captureException()` automatically when the function was available. Version 5 does not.
To report caught exceptions as Sentry issues in a Symfony application, install and configure
[`sentry/sentry-symfony`](https://docs.sentry.io/platforms/php/guides/symfony/) and `symfony/monolog-bundle`.
Register the official exception handler in the consuming application, for example in `config/packages/monolog.yaml`:

```yaml
services:
    Sentry\Monolog\ExceptionToSentryIssueHandler:
        arguments:
            $hub: '@Sentry\State\HubInterface'
            $level: error

monolog:
    handlers:
        sentry_exceptions:
            type: service
            id: Sentry\Monolog\ExceptionToSentryIssueHandler
```

The handler sends exceptions from the `exception` context to Sentry as issues. Its level and any configured Monolog
channels determine which records are sent; adjust them to your application. Make sure the logger injected into
`ExceptionLogger` uses this handler. The Sentry Logs handler sends log records to Sentry Logs and is separate from
issue reporting. Uncaught exceptions may already be reported by the Sentry Symfony integration; avoid reporting the
same exception twice. Exception data may contain sensitive information, so configure filtering appropriately.
This handler requires a Monolog logger; other PSR-3 implementations need their own integration.

After migration, verify an ordinary log message, a caught exception, and—if enabled—the resulting Sentry issue in the
target environment.
