# CHANGELOG

## 4.0.0 [BC Break]

* Changed: Replace `LogAwareTrait` with the constructor-injected `ExceptionLogger` decorator.
* Changed: Keep the optional Sentry integration in the decorator using `Sentry\captureException()`.
* Changed: Require `psr/log` 3.0.
* Changed: Require PHP 8.4


## 3.2.0 [BC Break]

* Change: [BC] Removed the check if the logger is not null. So Exceptions won't be lost.
* Change: Upgrade to PHP 8.3


# 3.1.0

* Changed: Upgrade to PHP 8.2 and improve code quality


# 3.0.0

* Changed: Upgrade to psr/log 3.0


# 2.0.0

* Changed: Integrate Sentry logging


# 1.0.0

* Initial version
