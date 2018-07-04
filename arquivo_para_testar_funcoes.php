<?php

include "include/all_include.php";
require 'include/apimagentophp/include/all_include.php';
global $magento_soap_user;
global $magento_soap_password;
global $store_id;
global $DEBUG;
global $shipping_method;

$obj_magento = magento_obj();
$session = magento_session();
echo "<pre>";
$cart_id = $obj_magento->shoppingCartCreate($session, $store_id);
echo "<h1>INFORMAÇÕES DA LOJA-PAYMENT:</h1> <BR>";
var_dump($obj_magento->shoppingCartPaymentList($session, $cart_id, $store_id));
echo "<h1>INFORMAÇÕES DA LOJA-SHIPPING:</h1> <BR>";
var_dump($obj_magento->shoppingCartShippingList($session, $cart_id, $store_id));


// include "include/all_include.php";
// $media_de_cada_passe = 0;
// $i=1;
// do {
//
//   $tempo = time();
//
//
//   echo "QTD de vezes: ".$i;
//
//   echo "";
//
//
//    echo "<br>";
//    echo "TEMPO: ". (time() - $tempo);
//    echo "<br><br><br></h2>";
//  $tempo_ate_agora = (time() - $tempo);
//  $media_de_cada_passe = ($media_de_cada_passe + $tempo_ate_agora)/2;
//
//
//  echo "<br>Media de cada lance: ".$media_de_cada_passe;
//  $i++;
// }
// while (($tempo_ate_agora + $media_de_cada_passe) < 0);

// $appId = "4946951783545211";
// $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
// $accesstoken = "APP_USR-4946951783545211-062613-e512bd2717f82eb16eb143eb18085bfe-327485416";
// $userid = '327485416';
// USUARIO DE TESTES
//  ["id"]=> int(327485416)
//  ["nickname"]=> string(8) "TT784263"
//  ["password"]=> string(10) "qatest7896"
//  ["site_status"]=> string(6) "active"
//  ["email"]=> string(31) "test_user_97680688@testuser.com" }
// $acessToken = APP_USR-4946951783545211-062613-e512bd2717f82eb16eb143eb18085bfe-327485416

//USUARIO DE TESTES 2
// ["id"]=> int(327509935)
//     ["nickname"]=> string(12) "TEST4CXNCJNZ"
//     ["password"]=> string(10) "qatest8331"
//     ["site_status"]=> string(6) "active"
//     ["email"]=> string(30) "test_user_2645635@testuser.com"
