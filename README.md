# Communitales Log Component

[![Codequality](https://github.com/communitales/log/actions/workflows/codequality.yml/badge.svg)](https://github.com/communitales/log/actions/workflows/codequality.yml)

A PSR-3 compatible logger decorator with convenient exception logging.

## Setup

Wrap any PSR-3 logger in `ExceptionLogger` and inject the decorator through its interface:

```php
use Communitales\Component\Log\ExceptionLogger;

$exceptionLogger = new ExceptionLogger($psrLogger);
```

Symfony example:

```yaml
services:
    Communitales\Component\Log\ExceptionLogger:
        arguments:
            $logger: '@logger'

    Communitales\Component\Log\ExceptionLoggerInterface:
        alias: Communitales\Component\Log\ExceptionLogger
```

## Usage

The decorator implements `Psr\Log\LoggerInterface`, so it supports ordinary log messages as well as exceptions:

```php
use Communitales\Component\Log\ExceptionLoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;

final readonly class SomeService
{
    public function __construct(private ExceptionLoggerInterface $logger)
    {
    }

    public function execute(): void
    {
        $this->logger->notice('Starting operation');

        try {
            // Perform operation.
        } catch (RuntimeException $exception) {
            $this->logger->logException(
                $exception,
                ['operation' => 'example'],
                LogLevel::ERROR,
            );
        }
    }
}
```

The exception is passed to the underlying logger using the standard `exception` context key.

## Sentry integration

The logger has no direct Sentry integration. To report caught exceptions as Sentry issues in a Symfony application,
install and configure [`sentry/sentry-symfony`](https://docs.sentry.io/platforms/php/guides/symfony/) and
`symfony/monolog-bundle`. Register the official exception handler in the consuming
application, for example in `config/packages/monolog.yaml`:

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

Previously, installing Sentry enabled exception reporting automatically. Applications relying on that behavior must
configure a handler explicitly when upgrading.

The positional arguments of `logException()` have also changed from `($exception, $level, $context)` to
`($exception, $context, $level)`. Update calls that pass a level or context positionally. Named arguments using
`level` and `context` do not need to be reordered.

## Contributing and security

See [CONTRIBUTING.md](CONTRIBUTING.md) for development and contribution guidance, and [SECURITY.md](SECURITY.md) for
supported versions and private vulnerability reporting.
