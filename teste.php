<?php
include "include/all_include.php";

$appId = "4946951783545211";
$secretkey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
$userId = "327485416";
$accesstoken = "APP_USR-4946951783545211-061313-805c369368427433b53b7d9373bb63bf-327485416";

$COD = "1732349386";

$meli = new Meli($appId, $secretKey);

$params = array('access_token' => $accesstoken);


$response = $meli->get("/orders/$COD", $params);

echo "<pre>";

// if($DEBUG == true) var_dump($response); //DEBUG

$dadosVenda = new stdClass;

//------------PRODUTO--------
foreach ($response['body']->order_items as $key => $value) {
  $dadosVenda->mlb_produto = $value->item->id;
  $dadosVenda->sku_produto = retornar_SKU($dadosVenda->mlb_produto);
  $dadosVenda->nome_produto = $value->item->title;
  $dadosVenda->qtd_produto = $value->quantity;
  $dadosVenda->preco_unidade_produto = $value->unit_price;
  $dadosVenda->preco_total_produto = $value->full_unit_price;
}

var_dump($dadosVenda);




//
//
//
//
//   $meli = new Meli($appId, $secretkey);
//
//   $params = array('access_token' => $accesstoken,
//   'seller' => "327485416"
//   );
//
//
//
//   // $params = array('access_token' => $accesstoken,
//   // 'seller' => "327485416",
//   // 'order.date_created.from' => "2018-06-11T00:00:00.000-00:00",
//   // 'order.date_created.to' => "2018-06-13T00:00:00.000-00:00"
//   // );
//   $response = $meli->get("/orders/search", $params);
//
//
//   $idOrders = new stdClass;
//
//   foreach ($response['body']->results as $key => $value) {
//     $i = "i".$key;
//     $idOrders->$i = $value->payments[0]->order_id;
//   }
//
// var_dump($idOrders);
