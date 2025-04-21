<?php
// lib/BithumbAPI.php

require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;

class BithumbAPI {
    private string $accessKey;
    private string $secretKey;
    private string $apiUrl = 'https://api.bithumb.com';

    public function __construct(string $accessKey, string $secretKey) {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /** JWT 생성 **/
    private function generateToken(array $params = []): string {
        $payload = [
            'access_key' => $this->accessKey,
            'nonce'      => bin2hex(random_bytes(16)),
            'timestamp'  => round(microtime(true) * 1000),
        ];
        if (!empty($params)) {
            ksort($params);
            $qs = http_build_query($params);
            $payload['query_hash']     = hash('sha512', $qs);
            $payload['query_hash_alg'] = 'SHA512';
        }
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    /** 공통 요청 처리 **/
    private function requestWithAuth(string $method, string $path, array $params = []): array {
        if (!empty($params)) {
            ksort($params);
        }
        $token = $this->generateToken($params);
        $url   = $this->apiUrl . $path;
        $hdrs  = [
            "Authorization: Bearer {$token}",
            "Accept: application/json",
        ];

        $ch = curl_init();
        if (strtoupper($method) === 'GET') {
            if (!empty($params)) {
                $url .= '?' . http_build_query($params);
            }
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            $hdrs[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true) ?: [];
    }

    public function getAccounts(): array {
        return $this->requestWithAuth('GET', '/v1/accounts');
    }

    public function getOrdersChance(string $market): array {
        return $this->requestWithAuth('GET', '/v1/orders/chance', ['market' => $market]);
    }

    public function getOrdersList(array $params = []): array {
        return $this->requestWithAuth('GET', '/v1/orders', $params);
    }

    public function getOrder(string $uuid): array {
        return $this->requestWithAuth('GET', '/v1/order', ['uuid' => $uuid]);
    }

    public function placeOrder(array $params): array {
        return $this->requestWithAuth('POST', '/v1/orders', $params);
    }
}
