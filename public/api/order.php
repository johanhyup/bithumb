<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../lib/BithumbAPI.php';

define('ACCESS_KEY', 'real key');
define('SECRET_KEY', 'real key');

if (empty($_GET['uuid'])) {
    echo json_encode(['error'=>'uuid parameter is required'], JSON_UNESCAPED_UNICODE);
    exit;
}

$api    = new BithumbAPI(ACCESS_KEY, SECRET_KEY);
$result = $api->getOrder((string)$_GET['uuid']);

echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
