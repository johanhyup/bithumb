<?php
// 파일명: api/krw_deposit_api.php

require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

header('Content-Type: application/json; charset=utf-8');

// 환경변수에서 API 키 불러오기
$accessKey = '9b75dcaf4a9bda06be676d17fbca1fcc7aac22b676dbe8';
$secretKey = 'YTQxN2I4ZDA4MTZmODkwMmRhYWI0ZjlkZGEwMGNlMTc2YjE0ZmJjYjFmY2M3ZWFmMjBiMjc2ZDdlMDhjMA==';

$params = [];
if (isset($_GET['state'])) {
    $params['state'] = $_GET['state'];
}
if (isset($_GET['uuids']) && is_array($_GET['uuids'])) {
    $params['uuids'] = $_GET['uuids'];
}
if (isset($_GET['txids']) && is_array($_GET['txids'])) {
    $params['txids'] = $_GET['txids'];
}
if (isset($_GET['page'])) {
    $params['page'] = (int)$_GET['page'];
}
if (isset($_GET['limit'])) {
    $params['limit'] = (int)$_GET['limit'];
}
if (isset($_GET['order_by'])) {
    $params['order_by'] = $_GET['order_by'];
}

// JWT 페이로드 생성
$payload = [
    'access_key' => $accessKey,
    'nonce'      => bin2hex(random_bytes(16)),
    'timestamp'  => round(microtime(true) * 1000),
];
if (!empty($params)) {
    ksort($params);
    $qs = http_build_query($params);
    $payload['query_hash']     = hash('sha512', $qs);
    $payload['query_hash_alg'] = 'SHA512';
}

// 토큰 생성
$jwt = JWT::encode($payload, $secretKey, 'HS256');
$headers = [
    "Authorization: Bearer {$jwt}",
    "Accept: application/json"
];

// 요청 URL 구성
$queryString = !empty($params) ? '?' . http_build_query($params) : '';
$url = 'https://api.bithumb.com/v1/deposits/krw' . $queryString;

// cURL 호출
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

// 결과 출력
echo $response;
