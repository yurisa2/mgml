<?php
require 'include/all_include.php';

$MLB = $_GET["MLB"];
$SKU = $_GET["SKU"];

if(!isset($MLB) || !isset($SKU))
{
  echo "Faltando MLB ou SKU, Saindo Fora";
  exit;
}

$meli = new Meli($appId, $secretKey);

echo '<pre>';


$params = array('access_token' => $access_token);

  #this body will be converted into json for you
$body = array(
  'attributes' =>
  array(
    array(
    'id' => "MODEL",
    'value_name' => $SKU)
  ));

$response = $meli->put('/items/MLB'.$MLB, $body, $params);


var_dump($response);
echo '</pre>';
