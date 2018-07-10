<?php
require_once "include/all_include.php";
require_once 'include/apimagentophp/orderAdd-t.php';

echo "<pre>";


$Magento_order = retornaObjMl();

$teste = new Magento_orders($Magento_order);

var_dump($teste->CustomerCreate());
var_dump($teste->AddressCreate());
var_dump($teste->CartCreate());
var_dump($teste->ProductAdd());
var_dump($teste->ProductList());
var_dump($teste->CustomerSet());
var_dump($teste->CustomerAddresses());
var_dump($teste->ShippingMethod());
var_dump($teste->PaymentMethod());
var_dump($teste->CartOrder());
var_dump($teste->AddComment());
