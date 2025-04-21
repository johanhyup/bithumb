<?php
// api/place_order.php

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';

define('ACCESS_KEY', 'real key');
define('SECRET_KEY', 'real key');

$api = new BithumbAPI(ACCESS_KEY, SECRET_KEY);

// POST 바디로 넘길 파라미터 (form‑urlencoded)
$params = [
    'market'   => 'KRW-BTC',
    'side'     => 'bid',
    'ord_type' => 'limit',
    'volume'   => '0.0001',
    'price'    => '50000000'
];

$result = $api->placeOrder($params);
echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
