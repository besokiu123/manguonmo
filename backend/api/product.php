<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/_products.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id || !isset($products[$id])) { http_response_code(404); echo json_encode(['success'=>false,'message'=>'Not found']); exit; }
echo json_encode(['success'=>true,'product'=>$products[$id]], JSON_UNESCAPED_UNICODE);
