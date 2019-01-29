# Communitales Log Component

Convenient integration for logging of messages and exceptions.

Usage
-----

```
class SomeClass
{

    use LogAwareTrait;

    public function testLog(): void
    {
        // Log your message including debug information
        $this->log('Test message', LogLevel::NOTICE, ['param1' => 'useful debug information']);
    }

    public function testLogException(): void
    {
        // Catch your exception
        $exception = new RuntimeException('Something gone wrong');

        // Log it with one line
        $this->logException($exception);
    }
}

```
