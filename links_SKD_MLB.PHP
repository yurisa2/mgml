<?php

$csv = file_get_contents('mlb_csv.csv');

echo "<pre>";

$tabela = array();

$linhas = explode(PHP_EOL, $csv);

foreach ($linhas as $key => $value) {
  $tabela[] = explode(',', $value);
}

foreach ($tabela as $key => $value) {


echo  "<a href=prod_put_sku.php?MLB=".$tabela[$key][1]."&SKU=".$tabela[$key][0].">
    prod_put_sku.php?MLB=".$tabela[$key][1]."&SKU=".$tabela[$key][0]."</a>";


}



var_dump($tabela[9][1]);
