<?php
// Si la petición es a un archivo o directorio real, lo servimos normalmente
if (php_sapi_name() === 'cli-server') {
    $url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $url;

    if (is_file($file)) {
        return false; // PHP sirve el archivo directamente
    }
}

// Si no es un archivo real, siempre carga index.php
require_once 'index.php';
