# Migrating from 3.x to 4.x

## Requirements

Version 4 requires PHP 8.4 or newer and `psr/log` 3.x. Version 3.2 supported PHP 8.3 and `psr/log` 1.x, 2.x and
3.x. Check the compatibility of your other dependencies before upgrading. Update the package constraint with Composer:

```sh
composer require communitales/log:^4.0
```

## Replace the trait with an injected logger

`LogAwareTrait` has been removed. Inject `ExceptionLoggerInterface` into classes that log exceptions. It extends
`Psr\Log\LoggerInterface`, so it also provides the usual PSR-3 logging methods.

Before:

```php
use Communitales\Component\Log\LogAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

final class Importer implements LoggerAwareInterface
{
    use LogAwareTrait;

    public function report(\Throwable $exception): void
    {
        $this->log('Import failed', LogLevel::WARNING, ['source' => 'csv']);
        $this->logException($exception, LogLevel::WARNING, ['source' => 'csv']);
    }
}
```

After:

```php
use Communitales\Component\Log\ExceptionLoggerInterface;
use Psr\Log\LogLevel;

final readonly class Importer
{
    public function __construct(private ExceptionLoggerInterface $logger)
    {
    }

    public function report(\Throwable $exception): void
    {
        $this->logger->warning('Import failed', ['source' => 'csv']);
        $this->logger->logException($exception, LogLevel::WARNING, ['source' => 'csv']);
    }
}
```

The old protected `log()` accepted `(message, level, context)` and defaulted to `error`. The PSR-3 `log()` method
accepts `(level, message, context)` and requires a level. Alternatively, use a level-specific method such as
`error()` or `warning()`.

## Configure dependency injection

For Symfony, register the decorator and its interface in `config/services.yaml`:

```yaml
services:
    Communitales\Component\Log\ExceptionLogger:
        arguments:
            $logger: '@logger'

    Communitales\Component\Log\ExceptionLoggerInterface:
        alias: Communitales\Component\Log\ExceptionLogger
```

Remove `LoggerAwareInterface`, `LogAwareTrait`, and any explicit `setLogger()` calls from migrated classes. Keep any
global `LoggerAwareInterface` configuration that other classes still need. Outside Symfony, construct the decorator
with an existing PSR-3 logger: `new ExceptionLogger($psrLogger)`.

## Exception context and Sentry

The `logException()` argument order remains `(exception, level, context)` in version 4. Both versions put the
provided exception in the `exception` context key, overriding a conflicting key supplied by the caller. Version 3
also added a `trace` string automatically; version 4 does not. Update consumers that depend on `context['trace']`,
or explicitly add it where needed. Many log handlers can extract a trace from the exception itself.

Version 4 retains automatic Sentry exception reporting when the Sentry function is available. This changes in
version 5; see the [next migration guide](MIGRATING-4-to-5.md).
