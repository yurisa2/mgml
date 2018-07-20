<?php
require_once "include/all_include.php";

echo "<pre>";

var_dump(listaPedidoMLB());
//
$Magento_order = retornaObjMl();
$mlb = proximoPedidoMLB();
var_dump($mlb);

$teste = new Magento_order($Magento_order);

$pedidosFeitos = retornaPedidosfeitosMGML();
$string = implode(",",$mlb);
if(!strpos($pedidosFeitos, $string)){
  $id_customer = $teste->magento1_customerCustomerCreate();
  var_dump($id_customer);
  $customer_address = $teste->magento2_customerAddressCreate($id_customer);
  var_dump($customer_address);
  $id_carrinho = $teste->magento3_shoppingCartCreate();
  var_dump($id_carrinho);
  $add_produto = $teste->magento4_shoppingCartProductAdd($id_carrinho);
  var_dump($add_produto);
  $produtos_carrinho = $teste->magento5_shoppingCartProductList($id_carrinho);
  var_dump($produtos_carrinho);
  $customerSet = $teste->magento6_shoppingCartCustomerSet($id_carrinho,$id_customer);
  var_dump($customerSet);
  $customerAddressSet = $teste->magento7_shoppingCartCustomerAddresses($id_carrinho);
  var_dump($customerAddressSet);
  $customerEntregaSet = $teste->magento8_shoppingCartShippingMethod($id_carrinho);
  var_dump($customerEntregaSet);
  $customerPagamentoSet = $teste->magento9_shoppingCartPaymentMethod($id_carrinho);
  var_dump($customerPagamentoSet);
  $order = $teste->magento10_shoppingCartOrder($id_carrinho);
  var_dump($order);

  if($order == true) var_dump(escrevePedidoMGML($mlb));
}

var_dump(escrevePedidoMLB($mlb));
