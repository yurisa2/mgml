<?php
include "include/all_include.php";
include "include/apimagentophp/orderAdd.php";


echo "<pre>";


function retornaDadosOrders(){
$orders = retornaOrders();

//var_dump($orders); //DEBUG


$magento_orders = new stdClass;

foreach ($orders as $key => $value) {
  $dados_order = retornaDadosVenda($value);

  $buyerid = $dados_order->id_comprador;
  $magento_orders->$buyerid->order_id[] = $dados_order->id_order;
  $magento_orders->$buyerid->mlb_produto[] = $dados_order->mlb_produto;
  $magento_orders->$buyerid->sku_produto[] = $dados_order->sku_produto;
  $magento_orders->$buyerid->nome_produto[] = $dados_order->nome_produto;
  $magento_orders->$buyerid->qtd_produto[] = $dados_order->qtd_produto;
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

var_dump($dadosVenda);
