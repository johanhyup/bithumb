<?php
// api/orders_chance.php

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';

define('ACCESS_KEY', '9b75dcaf4a9bda06be676d17fbca1fcc7aac22b676dbe8');
define('SECRET_KEY', 'YTQxN2I4ZDA4MTZmODkwMmRhYWI0ZjlkZGEwMGNlMTc2YjE0ZmJjYjFmY2M3ZWFmMjBiMjc2ZDdlMDhjMA==');

$market = isset($_GET['market']) ? trim($_GET['market']) : null;
if (!$market) {
    echo json_encode(['error' => 'market parameter is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$api = new BithumbAPI(ACCESS_KEY, SECRET_KEY);
$result = $api->getOrdersChance($market);
echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
