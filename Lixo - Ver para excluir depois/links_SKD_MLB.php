<?php

require 'include/all_include.php';

$csv = file_get_contents('mlb_csv.csv');

echo "<pre>";

$tabela = array();

$linhas = explode(PHP_EOL, $csv);

foreach ($linhas as $key => $value) {
  $tabela[] = explode(',', $value);
}

$meli = new Meli($app_Id, $secret_Key);

$params = array('access_token' => token());

foreach ($tabela as $key => $value) {


  // echo  "<a href=prod_put_sku.php?MLB=".$tabela[$key][1]."&SKU=".$tabela[$key][0].">
  //     prod_put_sku.php?MLB=".$tabela[$key][1]."&SKU=".$tabela[$key][0]."</a>";

  $body = array(
    'attributes' =>
    array(
      array(
      'id' => "MODEL",
      'value_name' => $tabela[$key][0])
    ));

  $response = $meli->put('/items/MLB'.$tabela[$key][1], $body, $params);

  if($response["httpCode"] == 200)

  echo "<H1> DEU CERTO! </H1><h3>Veja os dados abaixo:</h3><br>";
  var_dump($response);

}

// var_dump($tabela[9][1]);
