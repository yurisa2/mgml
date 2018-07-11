<?php
require_once "include/all_include.php";
require_once 'include/apimagentophp/orderAdd.php';

echo "<pre>";

$mlb = proximoPedidoMLB();
$mgnt = "210000496877";

var_dump(escrevePedidoMGML($mlb, $mgnt));


//$teste = new Magento_order($Magento_order);
//var_dump($teste);

$tempo_inicial = (time() - $tempo_inicial);
echo $tempo_inicial;
