<?php
require_once "include/all_include.php";
require_once 'include/apimagentophp/orderAdd.php';

echo "<pre>";


$Magento_order = retornaObjMl();

$teste = new Magento_orders($Magento_order);
var_dump($teste);
