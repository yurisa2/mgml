<?php
include "include/all_include.php";
//
$appId = "4946951783545211";
$secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
$accesstoken = "APP_USR-4946951783545211-062013-44ff2c77c72f01da903dad3623758340-327485416";
$userid = '327485416';

global $app_Id;
global $secret_Key;
global $DEBUG;
global $user_id;

$DEBUG = TRUE;

echo "<pre>";

$meli = new Meli($app_Id, $secret_Key);


 $params = array('access_token' => token(),
'seller' => $user_id, 'order.status' => 'paid');

$response = $meli->get("/orders/search", $params);
//var_dump($response['body']->results);
var_dump($response['body']->results[0]->buyer->id);
var_dump($response['body']->results[0]->id);
var_dump($response['body']->results[1]->buyer->id);
var_dump($response['body']->results[1]->id);
var_dump($response['body']->results[2]->buyer->id);
var_dump($response['body']->results[2]->id);



var_dump($listagem);
//
//  $buyer = new stdClass();
//
// foreach($response['body']->results as $key => $value){
//
//  $buyer->id = $value->buyer->id;
//
//   if($buyer->id == $value->buyer->id){
//     $buyer->$key = $value->id;
//     var_dump($buyer);
//   }
//
// }
