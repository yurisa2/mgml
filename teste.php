<?php
include "include/all_include.php";
//
// $appId = "4946951783545211";
// $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
// $accesstoken = "APP_USR-4946951783545211-062213-b412750eeebd43bbfb6fd03db8127594-327485416";
// $userid = '327485416';
//
//
//
// global $app_Id;
// global $secret_Key;
// global $DEBUG;
// global $user_id;



echo "<pre>";

$orders = retornaOrders();


var_dump($orders);



foreach ($orders as $key => $value) {
  $dados_order = retornaDadosVenda($orders->$key);

  //$buyer = $dados_order->id_comprador;

  $order_info['buyerid'] = $dados_order->id_comprador;
  $order_info['orderid'] = $dados_order->id_order;

  $aux = $dados_order->id_comprador;
  $customer = '$customer_'.$aux;
  //echo "Customer ID: ".$customer;

$var = json_decode(file_get_contents('orderid.json'));
$buyer = $var->buyerid;
$order = $var->orderid;
$customer = new stdClass;

var_dump($dados_order->id_comprador);

if($dados_order->id_comprador == $buyer){

  $i = 0;
  $id_order= "id_order".$i;
  $mlb_produto = "mlb_produto".$i;
  $sku_produto = "sku_produto".$i;
  $nome_produto = "nome_produto".$i;
  $qtd_produto = "qtd_produto".$i;

  $customer->id_order = $dados_order->id_order;
  $customer->$key = $order;
  $customer->mlb_produto = $dados_order->mlb_produto;
  $customer->sku_produto = $dados_order->sku_produto;
  $customer->nome_produto = $dados_order->nome_produto;
  $customer->id_comprador = $dados_order->id_comprador;
  $customer->nome_comprador = $dados_order->nome_comprador;
  $customer->sobrenome_comprador = $dados_order->sobrenome_comprador;
  $customer->email_comprador = $dados_order->email_comprador;
  $customer->numero_documento_comprador = $dados_order->numero_documento_comprador;
  $customer->telefone_comprador = $dados_order->cod_area_comprador.$dados_order->telefone_comprador;
  $customer->id_shipping = $dados_order->id_shipping;
  $customer->rua = $dados_order->rua.", ".$dados_order->numero." - ".$dados_order->bairro;
  $customer->cep = $dados_order->cep;
  $customer->cidade = $dados_order->cidade;
  $customer->estado = $dados_order->estado;
  $customer->pais = $dados_order->pais;

file_put_contents('orderid.json', json_encode($order_info));

  $i++;
}else{
  $customer->id_order = $orders->$key;
  $customer->mlb_produto = $dados_order->mlb_produto;
  $customer->sku_produto = $dados_order->sku_produto;
  $customer->nome_produto = $dados_order->nome_produto;
  $customer->qtd_produto = $dados_order->qtd_produto;
  $customer->id_comprador = $dados_order->id_comprador;
  $customer->nome_comprador = $dados_order->nome_comprador;
  $customer->sobrenome_comprador = $dados_order->sobrenome_comprador;
  $customer->email_comprador = $dados_order->email_comprador;
  $customer->numero_documento_comprador = $dados_order->numero_documento_comprador;
  $customer->telefone_comprador = $dados_order->cod_area_comprador.$dados_order->telefone_comprador;
  $customer->id_shipping = $dados_order->id_shipping;
  $customer->rua = $dados_order->rua.", ".$dados_order->numero." - ".$dados_order->bairro;
  $customer->cep = $dados_order->cep;
  $customer->cidade = $dados_order->cidade;
  $customer->estado = $dados_order->estado;
  $customer->pais = $dados_order->pais;


}


file_put_contents('orderid.json', json_encode($order_info));

var_dump($customer);
}

// $customer->id_order = $orders->$key;
// $customer->mlb_produto = $dados_order->mlb_produto;
// $customer->sku_produto = $dados_order->sku_produto;
// $customer->nome_produto = $dados_order->nome_produto;
// $customer->qtd_produto = $dados_order->qtd_produto;
// $customer->nome_comprador = $dados_order->nome_comprador;
// $customer->sobrenome_comprador = $dados_order->sobrenome_comprador;
// $customer->email_comprador = $dados_order->email_comprador;
// $customer->numero_documento_comprador = $dados_order->numero_documento_comprador;
// $customer->telefone_comprador = $dados_order->cod_area_comprador.$dados_order->telefone_comprador;
// $customer->id_shipping = $dados_order->id_shipping;
// $customer->rua = $dados_order->rua.", ".$dados_order->numero." - ".$dados_order->bairro;
// $customer->cep = $dados_order->cep;
// $customer->cidade = $dados_order->cidade;
// $customer->estado = $dados_order->estado;
// $customer->pais = $dados_order->pais;
