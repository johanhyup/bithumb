<?php
// api/accounts.php

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';

define('ACCESS_KEY', 'real key');
define('SECRET_KEY', 'real key');

$api = new BithumbAPI(ACCESS_KEY, SECRET_KEY);
echo json_encode($api->getAccounts(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
