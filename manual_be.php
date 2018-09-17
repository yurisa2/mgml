<?php
ini_set("error_reporting",E_ALL);
ini_set('display_errors', 1);
require_once 'include/all_include.php';

$acao = $_GET["optradio"];

$mlb = $_GET["mlb"];

if($mlb == ''){
  if( ($acao == 'setloop') || ($acao == 'Sinc') ){
    echo "Favor digite o MLB";
    exit;
  }
}
else{
$mlb = strtoupper($mlb);
$mlb = trim($mlb);
}
// var_dump($_GET);
// exit;

echo '<pre>';

$sec_ini = time();

// magento_write_all_products();
// magento_write_last_product_start('EP-13-12111');
// var_dump(magento_get_last_product_update_start()->sku);

// update_product_bool('EP-51-60031');
// var_dump(update_product_bool('EP-51-60021'));

if($acao == "Sinc")
{
  var_dump(atualizaProdML($mlb));
}

if($acao == "setloop")
{
  var_dump(setarInicioLoop($mlb));
}

if($acao == "listMgt")
{
  var_dump(listaProdMgt());
}

if($acao == "listml")
{
  var_dump(listaProdML());
}

if($acao == "mail")
{
var_dump(testmail());
}
// var_dump(merco_product_summary("EP-51-35011"));

$secs = time() - $sec_ini;

echo '<br>'.$secs;
echo '<br>';


$secs = time() - $sec_ini;

echo '<br>'.$secs .' Seg';

?>
