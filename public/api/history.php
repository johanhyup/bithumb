<?php
header('Content-Type: application/json; charset=utf-8');
require("xcoin_api_client.php");
$api = new XCoinAPI("real key", "real key");

$rgParams['offset']           = '0';
$rgParams['count']            = '20';
$rgParams['searchGb']         = '0';
$rgParams['order_currency']   = 'BTC';
$rgParams['payment_currency'] = 'KRW';

$result = $api->xcoinApiCall("/info/user_transactions", $rgParams);

// 객체형 결과를 무조건 배열로 변환
$result = json_decode(json_encode($result), true);

if (isset($result['data']) && is_array($result['data'])) {
    echo json_encode($result['data'], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
} else {
    // 에러, 빈데이터 fallback
    echo json_encode([], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
}
