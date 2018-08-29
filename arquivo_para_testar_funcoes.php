<?php
ini_set("error_reporting",E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "include/all_include.php";
$DEBUG = false;
echo "<pre>";

$teste = new error_handling("titulo", "nome_funcao", "saida", "tipo");
$teste->files();

// $mensagem = file_get_contents('error_files/error_log.json');
// var_dump($mensagem);
// if (count($mensagem) < 100)
// {
//   $titulo = "Erros sei lá";
//   $corpo_email = '';
//   foreach ($mensagem as $key => $value) {
//     foreach ($mensagem[$key] as $i => $values) {
//       $corpo_email.= $i.": ".$values."<br>";
//     }
//   }
//   // var_dump($corpo_email);
//
// }else echo "ainda não";
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
