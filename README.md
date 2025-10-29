# Sido Project

## Local Quality Tooling

The project uses the upstream releases of PHPStan, Rector, and PHP-CS-Fixer for static analysis, automated refactoring, and coding standards enforcement.

Install the Composer dependencies and run the following commands to execute all checks:

```bash
composer install
vendor/bin/phpstan analyse --configuration=phpstan.neon
vendor/bin/rector process --dry-run --config=rector.php
vendor/bin/php-cs-fixer fix --dry-run --config=.php-cs-fixer.php
```
