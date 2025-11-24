<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require __DIR__ . '/_products.php';
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);
$ordersFile = $dataDir . '/orders.json';
if (!file_exists($ordersFile)) file_put_contents($ordersFile, json_encode([]));

function out($d,$c=200){ http_response_code($c); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }

$input = file_get_contents('php://input');
$req = json_decode($input, true);
if (!is_array($req)) out(['success'=>false,'message'=>'Invalid JSON'],400);

$customer = isset($req['customer']) ? $req['customer'] : null;
if (!$customer || !isset($customer['name']) || !isset($customer['phone']) || !isset($customer['address'])) out(['success'=>false,'message'=>'Missing customer info'],400);

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) out(['success'=>false,'message'=>'Cart is empty'],400);

$items = [];$total = 0;
foreach($_SESSION['cart'] as $id => $qty){ if(!isset($products[$id])) continue; $p=$products[$id]; $subtotal=$p['price']*$qty; $total += $subtotal; $items[]=['id'=>$p['id'],'name'=>$p['name'],'price'=>$p['price'],'qty'=>$qty,'subtotal'=>$subtotal]; }
if (empty($items)) out(['success'=>false,'message'=>'No valid items in cart'],400);

$order = [
    'id' => time() . rand(100,999),
    'created_at' => date('c'),
    'customer' => $customer,
    'items' => $items,
    'total' => $total,
    'status' => 'pending'
];

$fp = fopen($ordersFile, 'c+');
if (!$fp) out(['success'=>false,'message'=>'Cannot open orders file'],500);
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$list = [];
if ($contents) { $decoded = json_decode($contents, true); if (is_array($decoded)) $list = $decoded; }
$list[] = $order;
ftruncate($fp,0); rewind($fp); fwrite($fp, json_encode($list, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)); fflush($fp); flock($fp, LOCK_UN); fclose($fp);

$_SESSION['cart'] = [];

out(['success'=>true,'order'=>$order]);
