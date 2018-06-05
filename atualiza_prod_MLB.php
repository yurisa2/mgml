<?php

function atualizaProdMLB($SKU,$MLB)
{
  require 'include/all_include.php';
  $produto = magento_product_summary($SKU);
  $title = $produto['name'];
  $description = $produto['description'];
  $price = round($produto['price'],2);
  $available_quantity = $produto['qty_in_stock'];


  $meli = new Meli($app_Id, $secret_Key);

  $params = array('access_token' => token());

    $bodyDescript = array(
      'plain_text' => $description

    );

    $body = array(
      'title' => $title,
      'price' => $price,
      'available_quantity' => $available_quantity,
      'attributes' =>
      array(
        array(
        'name' => "Marca",
        'value_name' => "Easypath"),
        array(
        'id' => "MODEL",
        'value_name' => $SKU)
        )
        //array(
        //'warranty' => "")
    );

$response = $meli->put('/items/MLB'.$MLB, $body, $params);

$responseDescript = $meli->put('/items/MLB'.$MLB.'/description', $bodyDescript, $params);
echo "<pre>";
if($response["httpCode"] == 200)

echo "<H1> DEU CERTO! </H1><h3>Veja os dados abaixo:</h3><br>";
var_dump($response);

if($responseDescript["httpCode"] == 200)

echo "<H1> DEU CERTO! </H1><h3>Veja os dados abaixo:</h3><br>";
var_dump($responseDescript);

}

atualizaProdMLB("EP-51-60106","1038882076");
