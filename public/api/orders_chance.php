<?php
// api/orders_chance.php

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';

define('ACCESS_KEY', 'real key');
define('SECRET_KEY', 'real key');

$market = isset($_GET['market']) ? trim($_GET['market']) : null;
if (!$market) {
    echo json_encode(['error' => 'market parameter is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$api = new BithumbAPI(ACCESS_KEY, SECRET_KEY);
$result = $api->getOrdersChance($market);
echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
