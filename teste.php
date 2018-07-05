<?php
require_once "include/all_include.php";

echo "<pre>";


//LEMBRAR DE ARRUMAR CAMPO SKU E O RESTO DESTE ARQUIVO
//$DEBUG = true;
function retornaDadosOrders(){
  $orders = retornaOrders();

  //var_dump($orders); //DEBUG


  $magento_orders = new stdClass;
  $teste = "EP-51-40971";
  foreach ($orders as $key => $value) {
    $dados_order = retornaDadosVenda($value);

    $buyerid = $dados_order->id_comprador;
    $magento_orders->$buyerid->id_order[] = $dados_order->id_order;
    $magento_orders->$buyerid->mlb_produto[] = $dados_order->mlb_produto;
    $magento_orders->$buyerid->sku_produto[] = $teste;
    $magento_orders->$buyerid->nome_produto[] = $dados_order->nome_produto;
    $magento_orders->$buyerid->qtd_produto[] = $dados_order->qtd_produto;
    $teste++;
  }
  return $magento_orders;
}


$orders = retornaOrders();

$dadosVenda = retornaDadosOrders();

foreach ($orders as $key => $value) {
  $dados_order = retornaDadosVenda($value);
  $buyerid = $dados_order->id_comprador;
  $dadosVenda->$buyerid->preco_unidade_produto[] = $dados_order->preco_unidade_produto;
  $dadosVenda->$buyerid->preco_total_produto[] = $dados_order->preco_total_produto;

  $dadosVenda->$buyerid->id_meio_pagamento = $dados_order->id_meio_pagamento;
  $dadosVenda->$buyerid->tipo_pagamento = $dados_order->tipo_pagamento;
  $dadosVenda->$buyerid->custo_envio = $dados_order->custo_envio;
  $dadosVenda->$buyerid->total_pagar = $dados_order->total_pagar;
  $dadosVenda->$buyerid->status_pagamento = $dados_order->status_pagamento;


  $dadosVenda->$buyerid->id_shipping = $dados_order->id_shipping;
  $dadosVenda->$buyerid->rua = $dados_order->rua;
  $dadosVenda->$buyerid->numero = $dados_order->numero;
  $dadosVenda->$buyerid->bairro = $dados_order->bairro;
  $dadosVenda->$buyerid->cep = $dados_order->cep;
  $dadosVenda->$buyerid->cidade = $dados_order->cidade;
  $dadosVenda->$buyerid->estado = $dados_order->estado;
  $dadosVenda->$buyerid->pais = $dados_order->pais;

  $dadosVenda->$buyerid->id_comprador = $dados_order->id_comprador;
  $dadosVenda->$buyerid->apelido_comprador = $dados_order->apelido_comprador;
  $dadosVenda->$buyerid->email_comprador = $dados_order->email_comprador;
  $dadosVenda->$buyerid->cod_area_comprador = $dados_order->cod_area_comprador;
  $dadosVenda->$buyerid->telefone_comprador = $dados_order->cod_area_comprador.$dados_order->telefone_comprador;
  $dadosVenda->$buyerid->nome_comprador = $dados_order->nome_comprador;
  $dadosVenda->$buyerid->sobrenome_comprador = $dados_order->sobrenome_comprador;
  $dadosVenda->$buyerid->tipo_documento_comprador = $dados_order->tipo_documento_comprador;
  $dadosVenda->$buyerid->numero_documento_comprador = $dados_order->numero_documento_comprador;
}

//if($DEBUG == TRUE) {echo "Estrutura do OBJ $dadosVenda";var_dump($dadosVenda);}


$Magento_order = new stdClass();

foreach($dadosVenda as $key => $value){

  $Magento_order->order_id = $dadosVenda->$key->id_order;
  $Magento_order->mlb_produto = $dadosVenda->$key->mlb_produto;
  $Magento_order->sku_produto = $dadosVenda->$key->sku_produto;
  $Magento_order->nome_produto = $dadosVenda->$key->nome_produto;
  $Magento_order->qtd_produto = $dadosVenda->$key->qtd_produto;
  $Magento_order->preco_unidade_produto =$dadosVenda->$key->preco_unidade_produto;
  $Magento_order->preco_total_produto = $dadosVenda->$key->preco_total_produto;

  //--------------PAGAMENTO---------
  $Magento_order->id_meio_pagamento = $dadosVenda->$key->id_meio_pagamento;
  $Magento_order->tipo_pagamento = $dadosVenda->$key->tipo_pagamento;
  $Magento_order->custo_envio = $dadosVenda->$key->custo_envio;
  $Magento_order->total_pagar = $dadosVenda->$key->total_pagar;
  $Magento_order->status_pagamento = $dadosVenda->$key->status_pagamento;

  //-----------ENDEREÇO---------
  $Magento_order->rua = $dadosVenda->$key->rua;
  $Magento_order->numero = $dadosVenda->$key->numero;
  $Magento_order->bairro = $dadosVenda->$key->bairro;
  $Magento_order->cep = $dadosVenda->$key->cep;
  $Magento_order->cidade = $dadosVenda->$key->cidade;
  $Magento_order->estado = $dadosVenda->$key->estado;
  $Magento_order->pais = $dadosVenda->$key->pais;

  // ---------USUARIO---------
  // $Magento_order->id_comprador = $dadosVenda->$key->id_comprador;
  // $Magento_order->apelido_comprador = $dadosVenda->$key->apelido_comprador;
  // $Magento_order->email_comprador = "teste@mail.com.br";
  // $Magento_order->cod_area_comprador = $dadosVenda->$key->cod_area_comprador;
  // $Magento_order->telefone_comprador = $dadosVenda->$key->telefone_comprador;
  // $Magento_order->nome_comprador = "MLB-Novo Teste";
  // $Magento_order->sobrenome_comprador = $dadosVenda->$key->sobrenome_comprador;
  // $Magento_order->tipo_documento_comprador = $dadosVenda->$key->tipo_documento_comprador;
  // $Magento_order->numero_documento_comprador = "26526526500";

  $Magento_order->id_comprador = "01198765432";
  $Magento_order->apelido_comprador = "Tezinho";
  $Magento_order->email_comprador = "testezinhomag@mail.com.br";
  $Magento_order->cod_area_comprador = "53";
  $Magento_order->telefone_comprador = "232523322";
  $Magento_order->nome_comprador = "MLB-Teste Nome";
  $Magento_order->sobrenome_comprador = "nome";
  $Magento_order->tipo_documento_comprador = "CPF";
  $Magento_order->numero_documento_comprador = "11222333444";

}
//var_dump($Magento_order);

require_once 'include/apimagentophp/orderAdd.php';
require 'include/apimagentophp/include/all_include.php';


$teste = new Magento_order($Magento_order);

var_dump($teste);
