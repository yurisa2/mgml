<?php
include "include/all_include.php";

$meli = new Meli($app_Id, $secret_Key);

$params = array('access_token' => token());

$body = array(
  'site_id' => "MLB"
);
//1047551434

// $response = $meli->post('/users/test_user', $body, $params);

$response = $meli->get('/orders/search?buyer=buyer_id', $params);

echo "<pre>";
var_dump($response);

// USUARIO DE TESTES
//  ["id"]=> int(327485416)
//  ["nickname"]=> string(8) "TT784263"
//  ["password"]=> string(10) "qatest7896"
//  ["site_status"]=> string(6) "active"
//  ["email"]=> string(31) "test_user_97680688@testuser.com" }
//
//USUARIO DE TESTES 2
// ["id"]=> int(327509935)
//     ["nickname"]=> string(12) "TEST4CXNCJNZ"
//     ["password"]=> string(10) "qatest8331"
//     ["site_status"]=> string(6) "active"
//     ["email"]=> string(30) "test_user_2645635@testuser.com"
