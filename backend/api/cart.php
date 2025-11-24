<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require __DIR__ . '/_products.php';

function out($d,$c=200){ http_response_code($c); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'GET'){
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $items = [];$total=0;
    foreach($cart as $id=>$qty){ if(!isset($products[$id])) continue; $p=$products[$id]; $subtotal=$p['price']*$qty; $total += $subtotal; $items[]=['id'=>$p['id'],'name'=>$p['name'],'image'=>$p['image'],'price'=>$p['price'],'qty'=>$qty,'subtotal'=>$subtotal]; }
    out(['success'=>true,'items'=>$items,'total'=>$total]);
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!is_array($data)) $data = $_POST;

// remove
if (isset($data['action']) && $data['action']==='remove' && isset($data['id'])){
    $id = intval($data['id']); if (isset($_SESSION['cart'][$id])) unset($_SESSION['cart'][$id]); out(['success'=>true,'message'=>'removed']);
}

// clear
if (isset($data['action']) && $data['action']==='clear'){
    $_SESSION['cart'] = [];
    out(['success'=>true,'message'=>'cleared']);
}

// add/update
if (isset($data['id']) && isset($data['qty'])){
    $id = intval($data['id']); $qty = intval($data['qty']); if ($qty < 1) $qty = 1;
    if (!isset($products[$id])) out(['success'=>false,'message'=>'Invalid product'],400);
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] += $qty; else $_SESSION['cart'][$id] = $qty;
    out(['success'=>true,'cart'=>$_SESSION['cart']]);
}

out(['success'=>false,'message'=>'Invalid request'],400);
