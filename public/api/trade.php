<?php
// api/trade.php
require_once __DIR__ . '/vendor/autoload.php';
use Firebase\JWT\JWT;

header('Content-Type: application/json; charset=utf-8');

$accessKey = '9b75dcaf4a9bda06be676d17fbca1fcc7aac22b676dbe8';
$secretKey = 'YTQxN2I4ZDA4MTZmODkwMmRhYWI0ZjlkZGEwMGNlMTc2YjE0ZmJjYjFmY2M3ZWFmMjBiMjc2ZDdlMDhjMA==';

// POST 데이터 수신
$orderCurrency   = strtoupper($_POST['order_currency']   ?? '');
$paymentCurrency = strtoupper($_POST['payment_currency'] ?? '');
$units           = $_POST['units']                       ?? '';
$price           = $_POST['price']                       ?? '';
$type            = $_POST['type']                        ?? ''; // 'bid' 또는 'ask'

// 필수 파라미터 검증
if (!$orderCurrency || !$paymentCurrency || !$units || !$price || !in_array($type, ['bid','ask'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => '파라미터가 올바르지 않습니다.'
    ]);
    exit;
}

// 쿼리 문자열 생성
$body = [
    'order_currency'   => $orderCurrency,
    'payment_currency' => $paymentCurrency,
    'units'            => $units,
    'price'            => $price,
    'type'             => $type,
];
$query = http_build_query($body, '', '&');

// 해시 생성
$queryHash = hash('sha512', $query);

// JWT 페이로드 생성
$payload = [
    'access_key'      => $accessKey,
    'nonce'           => (string)time(),
    'query_hash'      => $queryHash,
    'query_hash_alg'  => 'SHA512',
];

// JWT 토큰 생성
$jwt = JWT::encode($payload, $secretKey);

// HTTP 헤더 설정
$headers = [
    "Authorization: Bearer {$jwt}",
    "Content-Type: application/x-www-form-urlencoded"
];

// cURL 초기화 및 요청
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.bithumb.com/v1/orders/place');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// 실행 및 응답 처리
$response = curl_exec($ch);
$err      = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode([
        'status'  => 'error',
        'message' => "cURL Error: {$err}"
    ]);
    exit;
}

// Bithumb API 응답 출력
echo $response;
