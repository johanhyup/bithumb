<?php

require_once __DIR__ . '/vendor/autoload.php';
use GuzzleHttp\Client;

function generateBithumbHeaders($endpoint, $params, $apiClientType, $apiKey, $secretKey) {
    $apiNonce = (string)round(microtime(true) * 1000);

    // 요청 파라미터에 endpoint 추가 (순서를 명시적으로 설정)
    $params = [
        'endpoint' => $endpoint,
        'order_currency' => $params['order_currency'],
        'payment_currency' => $params['payment_currency'],
    ];

    // URL 인코딩된 요청 파라미터 생성
    $requestParamsString = http_build_query($params, '', '&');
    $encodedParams = $requestParamsString;

    $delimiter = $apiClientType === "2" ? ';' : chr(0);
    $message = $endpoint . $delimiter . $encodedParams . $delimiter . $apiNonce;

    // 디버깅용 콘솔 출력: 조합된 메시지
    echo "Message for signing: " . $message . "\n";

    // HMAC-SHA512 해싱
    $signature = hash_hmac('sha512', $message, $secretKey, true);
    echo "HMAC-SHA512 (binary): " . bin2hex($signature) . "\n";

    // Base64 인코딩
    $apiSign = base64_encode($signature);
    echo "Generated Api-Sign (Base64): " . $apiSign . "\n";

    echo "Generated Nonce: " . $apiNonce . "\n";

    return [
        'api-client-type' => $apiClientType,
        'Api-Sign' => $apiSign,
        'Api-Nonce' => $apiNonce,
        'Api-Key' => $apiKey,
    ];
}

function fetchAccountInfo($apiKey, $secretKey) {
    $client = new Client();
    $endpoint = '/info/account';
    $params = [
        'order_currency' => 'BTC',
        'payment_currency' => 'KRW',
    ];
    $apiClientType = '2';

    $headers = generateBithumbHeaders($endpoint, $params, $apiClientType, $apiKey, $secretKey);

    try {
        $response = $client->request('POST', 'https://api.bithumb.com' . $endpoint, [
            'headers' => [
                'api-client-type' => $headers['api-client-type'],
                'Api-Sign' => $headers['Api-Sign'],
                'Api-Nonce' => $headers['Api-Nonce'],
                'Api-Key' => $headers['Api-Key']
            ],
            'form_params' => $params,
        ]);

        $body = $response->getBody();
        $data = json_decode($body, true);

        print_r($data);
    } catch (\Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

$apiKey = 'aad3915f9e1d4f57131384317d41771a';
$secretKey = '849725c5fc2adbfde63dad46b0ed9121';

fetchAccountInfo($apiKey, $secretKey);

?>