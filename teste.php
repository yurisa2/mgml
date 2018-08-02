<?php
ini_set("error_reporting",E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "include/all_include.php";
global $DEBUG;
echo "<pre>";
$DEBUG == true;

var_dump(listaPedidoMLB());


$Magento_order = retornaObjMl();
var_dump($Magento_order);
$nome = $Magento_order->nome_comprador;
$id_shipping = $Magento_order->id_shipping;

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

  if($order != 0)
  {
    var_dump(escrevePedidoMGML($mlb));

    $nome_arquivo = criaEtiqueta($id_shipping, $mlb, $nome, $order);

    $error_handling = new log("Novo Pedido MAGENTO", "Numero do Pedido MGT: $order", "Comprador: $nome", "nova compra");
    $error_handling->log_email = true;
    $error_handling->mensagem_email = "Nova compra que entrou no magento";
    $error_handling->etiqueta = $nome_arquivo;
    $error_handling->log_email = true;
    $error_handling->dir_files = "log/log.json";
    $error_handling->log_files = true;
    $error_handling->send_log_email();
    $error_handling->execute();
  }
}
else echo "Pedido já existente no MAGENTO";

var_dump(escrevePedidoMLB($mlb));
