<?php

declare(strict_types=1);

/**
 * Plug loader
 */
spl_autoload_register(function ($fqcn) {
    $prefix = 'Cooking\\Recipe\\Web';

    $base_dir = __DIR__ . '/apps/web';

    $n = strlen($prefix);
    if (strncmp($prefix, $fqcn, $n) !== 0) {
        return;
    }

    $relative_fqcn = substr($fqcn, $n);
    $file = $base_dir . '' . str_replace('\\', '/', $relative_fqcn) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});