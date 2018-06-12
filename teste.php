<?php
include "include/all_include.php";

$appId = "4946951783545211";
$secretkey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
$userId = "327485416";
$accesstoken = "APP_USR-4946951783545211-061213-ba35c88b7019cfdcaa820d86ecdec031-327485416";


  $meli = new Meli($appId, $secretkey);

  $params = array('access_token' => $accesstoken,
  'seller' => "327485416"
  );

  // $params = array('access_token' => $accesstoken,
  // 'seller' => "327485416",
  // 'order.date_created.from' => "2018-06-11T00:00:00.000-00:00",
  // 'order.date_created.to' => "2018-06-13T00:00:00.000-00:00"
  // );
  $response = $meli->get("/orders/search", $params);


  $idOrders = new stdClass;

  foreach ($response['body']->results as $key => $value) {
    $i = "i".$key;
    $idOrders->$i = $value->payments[0]->order_id;
  }

var_dump($idOrders->i0);
