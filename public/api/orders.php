<?php
// order.php

header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';
define('ACCESS_KEY', 'real key');
define('SECRET_KEY', 'real key');

// 디버그 로그
file_put_contents(__DIR__.'/order_debug.log', print_r([
    'time'    => date('Y-m-d H:i:s'),
    'headers' => function_exists('getallheaders') ? getallheaders() : [],
    'GET'     => $_GET,
    'POST'    => $_POST,
    'REQUEST' => $_REQUEST,
    'INPUT'   => file_get_contents('php://input'),
], true), FILE_APPEND);

$api = new BithumbAPI(ACCESS_KEY, SECRET_KEY);

$params = $_GET;

if (!empty($params['uuid'])) {
    // 개별 주문 조회
    $result = $api->getOrder((string)$params['uuid']);
} else {
    // 주문 목록 조회
    $result = $api->getOrdersList($params);
}

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
