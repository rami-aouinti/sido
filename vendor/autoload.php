<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\' => __DIR__ . '/../src/',
        'Symfony\\Component\\Mercure\\' => __DIR__ . '/../src/Symfony/Component/Mercure/',
        'Symfony\\Component\\Validator\\' => __DIR__ . '/../src/Symfony/Component/Validator/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});
