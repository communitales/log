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
                LogLevel::ERROR,
                ['operation' => 'example'],
            );
        }
    }
}
```

The exception is passed to the underlying logger using the standard `exception` context key.

## Sentry integration

If the optional `sentry/sentry` package is installed, `ExceptionLogger` also reports exceptions through `Sentry\captureException()`. Configure and initialize the Sentry SDK in the consuming application. No additional integration code is needed in this package.
