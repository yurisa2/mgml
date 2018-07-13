<?php
require_once "include/all_include.php";
require_once 'include/orderAdd.php';

echo "<pre>";

 var_dump(listaPedidoMLB());
//
$Magento_order = retornaObjMl();
//
$mlb = $Magento_order->order_id;
var_dump($mlb);

$teste = new Magento_order($Magento_order);

if($teste == true)
{
  var_dump(escrevePedidoMGML($mlb));

  var_dump(escrevePedidoMLB($mlb));
}

var_dump($teste);
