# CHANGELOG

## 5.0.0

### Breaking Changes

* Sentry is no longer invoked automatically. Configure the Sentry Monolog handler in the consuming application if
  caught exceptions should be reported.
* `logException()` now takes context as its second argument and level as its third; update positional calls.


## 4.0.0

### Breaking Changes

* Changed: Replace `LogAwareTrait` with the constructor-injected `ExceptionLoggerInterface` decorator.
* Changed: Keep the optional Sentry integration in the decorator using `Sentry\captureException()`.
* Changed: Require `psr/log` 3.0.
* Changed: Require PHP 8.4


## 3.2.0

### Breaking Changes

* Change: Removed the check if the logger is not null. So Exceptions won't be lost.
* Change: Upgrade to PHP 8.3


## 3.1.0

* Changed: Upgrade to PHP 8.2 and improve code quality


## 3.0.0

* Changed: Upgrade to psr/log 3.0


## 2.0.0

* Changed: Integrate Sentry logging


## 1.0.0

* Initial version
