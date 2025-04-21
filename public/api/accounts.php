<?php
// api/accounts.php

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';

define('ACCESS_KEY', '9b75dcaf4a9bda06be676d17fbca1fcc7aac22b676dbe8');
define('SECRET_KEY', 'YTQxN2I4ZDA4MTZmODkwMmRhYWI0ZjlkZGEwMGNlMTc2YjE0ZmJjYjFmY2M3ZWFmMjBiMjc2ZDdlMDhjMA==');

$api = new BithumbAPI(ACCESS_KEY, SECRET_KEY);
echo json_encode($api->getAccounts(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);