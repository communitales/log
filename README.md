# Communitales Log Component

Convenient integration for logging of messages and exceptions.

## Setup

Just use the `LogAwareTrait`.

Then set a logger via `$this->setLogger($logger);` or use the `Psr\Log\LoggerAwareInterface` as marker interface to
let the Dependency Injection do the magic for you.

Symfony example:

```
services:

    _instanceof:
        Psr\Log\LoggerAwareInterface:
            calls:
                - [setLogger, ['@logger']]

```

## Usage

```

use App\Component\Log\LogAwareTrait;
use Psr\Log\LoggerAwareInterface;
use \RuntimeException;

class SomeClass implements LoggerAwareInterface
{

    use LogAwareTrait;

    public function testLog(): void
    {
        // Log your message including debug information
        $this->log('Test message', LogLevel::DEBUG, ['param1' => 'useful debug information']);

        // Log an error
        $this->log('This should not happen');
    }

    public function testLogException(): void
    {
        try {
            throw new RuntimeException('Something gone wrong');
        } catch (RuntimeException $exception) {

            // Log with one line
            $this->logException($exception);
        }

    }
}

```


## Sentry logging out of the box

If the `\Sentry\captureException` function is available, exceptions will be logged also to Sentry.
