# Partner Gateway PHP Client
PHP Client for the [Partner API Gateway](https://github.com/Getsidecar/partner-api-gateway), used by [Adwords](https://github.com/Getsidecar/adwords/search?q=partner-gateway-client-php), that provides a thin wrapper abstracting technical details such as route formatting and distinguishing ephemeral errors from failed requests.

## Development
This project uses [Composer](https://getcomposer.org) and [PHPUnit 7](https://phpunit.de/getting-started/phpunit-7.html) for local testing and development.

To run tests:
```
composer install
phpunit tests/clienttest.php
```
