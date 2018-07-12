<?php
require_once "include/all_include.php";
require_once 'include/orderAdd.php';

echo "<pre>";

 var_dump(listaPedidoMLB());
//
// $dadosOrder = retornaObjMl();
//
// $mlb = $dadosOrder->order_id;
// var_dump($mlb);

$mlb = array("1732210033","1732210066");

$mgnt = "210000496823";

var_dump(escrevePedidoMGML($mlb, $mgnt));

var_dump(escrevePedidoMLB($mlb));

//$teste = new Magento_order($Magento_order);
//var_dump($teste);
