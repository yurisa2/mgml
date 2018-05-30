<?php
require 'include/all_include.php';

$MLB = $_GET["MLB"];
$SKU = $_GET["SKU"];

if(!isset($MLB) || !isset($SKU))
{
  echo "<h1>Faltando MLB ou SKU, Saindo Fora</h1>";
  exit;
}

$meli = new Meli($appId, $secretKey);

echo '<pre>';
$params = array('access_token' => $access_token);
$body = array(
  'attributes' =>
  array(
    array(
    'id' => "MODEL",
    'value_name' => $SKU)
  ));

$response = $meli->put('/items/MLB'.$MLB, $body, $params);

if($response["httpCode"] == 200)
echo "<H1> DEU CERTO! </H1><h3>Veja os dados abaixo:</h3><br>";
var_dump($response);
echo '</pre>';
