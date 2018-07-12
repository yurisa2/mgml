<?php
ini_set("error_reporting",E_ALL);
 include "include/all_include.php";

// require 'include/apimagentophp/include/all_include.php';
// global $DEBUG;
// global $app_Id;
// global $secret_Key;
// global $user_id;
//
 $meli = new Meli($app_Id, $secret_Key);
 $params = array('access_token' => token());
//$body = array('source' => "imagens/ep-51-40096_1.jpg");
//$response = $meli->get('/pictures/630052-MLB27727936061_072018' ,$params);
  //
  // $body = array('source' => "imagens/ep-51-40742.jpg");
  // $response = $meli->post('/pictures', $body, $params);

  $body = array('id' => "630052-MLB27727936061_072018");
  $response = $meli->post('/items/MLB1039710106/pictures', $body, $params);

    echo "<pre>";
   var_dump($response); //DEBUG

// $idimg = "811281-MLB27695604871_072018";

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
