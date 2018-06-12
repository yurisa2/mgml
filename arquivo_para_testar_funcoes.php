<?php
include "include/all_include.php";

$meli = new Meli($app_Id, $secret_Key);

$params = array('access_token' => token());
$MLB = "1040181530";

$body = array
(
  'attributes' =>
    array(
      array(
        'name' => "Marca",
        'value_name' => $marca),
// DEBUG AQUI PRECISA TER O SKU CASO CONTRARIO ELE ESCREVE A MARCA E ANULA  $SKU
//PROVAVELMENTE ESTARÃO SEM SKU ALGUNS DOS ANUNCIOS
       array(
         'id' => "MODEL",
         'value_name' => "EP-51-40657")
  )
);


$response = $meli->put('/items/MLB'.$MLB, $body, $params);

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
