<?php

$env = parse_ini_file(__DIR__ . '/../.env');

foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
}

return [
    'db_host' => $_ENV['DB_HOST'],
    'db_port' => $_ENV['DB_PORT'],
    'db_name' => $_ENV['DB_NAME'],
    'db_user' => $_ENV['DB_USER'],
    'db_pass' => $_ENV['DB_PASS'],
    'jwt_secret' => $_ENV['JWT_SECRET'],
    'jwt_expiry' => $_ENV['JWT_EXPIRY'],
];