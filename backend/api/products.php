<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/_products.php';
echo json_encode(['success'=>true,'products'=>array_values($products)], JSON_UNESCAPED_UNICODE);
