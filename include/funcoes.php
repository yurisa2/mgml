<?php

function token()
{
  $variavel = json_decode(file_get_contents("include/files/tokens.json"));
  $access_token = $variavel->access_token;
  $refresh_token = $variavel->refresh_token;

  if(time() > $variavel->expires_in)
  {
    renova($access_token,$refresh_token);
    $variavel = json_decode(file_get_contents("include/files/tokens.json"));
    $access_token = $variavel->access_token;
    $refresh_token = $variavel->refresh_token;

    return $access_token;
  }
  else return $access_token;
}

function renova($access_token,$refresh_token)
{
  global $app_Id;
  global $secret_Key;

  $meli = new Meli($app_Id, $secret_Key, $access_token,$refresh_token);
  $refresh = $meli->refreshAccessToken();
  $token_info["access_token"] = $refresh["body"]->access_token;
  $token_info["refresh_token"] = $refresh["body"]->refresh_token;
  $token_info["expires_in"] = time()+10000;
  file_put_contents("include/files/tokens.json",json_encode($token_info));
}

function lista_MLB() {
  global $app_Id;
  global $secret_Key;
  global $user_id;
  global $DEBUG;

  $meli = new Meli($app_Id, $secret_Key);
  $url = '/users/' . $user_id . '/items/search';
  $params = array(
    'access_token' => token(),
    'limit' => 100
  );
  $result = $meli->get($url, $params);
  //lê o json que contem o time() do ultimo email enviado
  if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
  $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

  //Se na requisição para atualizar o produto houver problema (retorno dif de 200)
  // ele entra no bloco de código
  if($result["httpCode"] != 200)
  {
    $nome_funcao = "lista_MLB";
    $saida = $response['body']->message;
    $titulo = "Erro no Script Mercado Livre";
    mandaEmail_files_db($nome_funcao,$saida,$titulo);
  }

  $limit = $result['body']->limit;

  if($result['body']->total > $result['body']->limit)
  {
    //Mandar e mail e o caralho pq nao fiz paginação (preguiça né)
    return 0;
    exit;
  }

  if ($DEBUG == true) var_dump($result); //DEBUG

  $listagem = $result['body']->results;
  $listagem = array_unique($listagem);

  return $listagem;
}

function ultimo_MLB()
{
  if(!file_exists("include/files/ultimo_MLB.json")) return false;
  else {
    $conteudo_arquivo = file_get_contents("include/files/ultimo_MLB.json");
    $retorno = json_decode($conteudo_arquivo);
    return $retorno;
  }
}

function proximo_MLB()
{
  global $DEBUG;
  $ultimo = ultimo_MLB();
  $ultimo = "MLB".$ultimo;
  $lista = lista_MLB();

  if($lista == 0){return 0;}

  $indice_ultimo = array_search($ultimo, $lista);
  $indice_proximo = $indice_ultimo+1;

  $valor_proximo = substr($lista[$indice_proximo], 3);
  $valor_zero = substr($lista["0"], 3);

  if ($DEBUG == true) var_dump($valor_zero); //$DEBUG
  if ($DEBUG == true) var_dump($valor_proximo); //$DEBUG

  if($indice_proximo+1 > count($lista)) return $valor_zero;
  else return $valor_proximo;
}

function atualizaProdMLB($SKU,$MLB)
{
  global $app_Id;
  global $secret_Key;
  global $DEBUG;
  global $ajuste_preco_multiplicacao;
  global $ajuste_estoque;
  global $ajuste_preco_soma;
  global $sufixo_prod;
  global $prefixo_prod;
  global $marca;


  $produto = magento_product_summary($SKU);
echo "produto";var_dump($produto);
  if(!$produto) return 0;
  $title = $prefixo_prod.$produto['name'].$sufixo_prod;

  if (strlen($title) > 60) $title = $prefixo_prod.$produto['name'];

  $price = round(($produto['price'] * $ajuste_preco_multiplicacao)+$ajuste_preco_soma,2);
  $available_quantity = floor($produto['qty_in_stock'] + ($produto['qty_in_stock']*$ajuste_estoque));

  echo "Price";var_dump($price); //DEBUG

  if($available_quantity < 0) $available_quantity = 0;

  $meli = new Meli($app_Id, $secret_Key);

  $params = array('access_token' => token());

  $body = array
  (
    'title' => $title,
    'price' => $price,
    'available_quantity' => $available_quantity,
    'attributes' =>
    array(
      array('name' => "Marca",
      'value_name' => $marca),
      array('id' => "MODEL",
      'value_name' => $SKU)
    )
  );


  $response = $meli->put('/items/MLB'.$MLB, $body, $params);

  echo "RESPONSE";var_dump($response['body']); //DEBUG
  //lê o json que contem o time() do ultimo email enviado
  if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
  $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

  //Se na requisição para atualizar o produto houver problema (retorno dif de 200)
  // ele entra no bloco de código
  if($response["httpCode"] != 200)
  {
    $nome_funcao = "atualizaProdMLB - SKU:$SKU - MLB:$MLB";
    $saida = $response['body']->message;
    $titulo = "Erro no Script Mercado Livre";
    mandaEmail_files_db($nome_funcao,$saida,$titulo);
  }
  //caso não tenha dado problema com a atualização do PRODUTO retorna 1
  else return "1";
}

function atualizaDescricaoMLB($SKU,$MLB)
{
  global $app_Id;
  global $secret_Key;
  global $DEBUG;


  $produto = magento_product_summary($SKU);

  if(!$produto) return 0;

  $description = $produto['description'];
  $meli = new Meli($app_Id, $secret_Key);
  $params = array('access_token' => token());

  $body = array
  (
    'plain_text' => $description
  );

  $response = $meli->put('/items/MLB'.$MLB.'/description', $body, $params);

  if ($DEBUG == true) var_dump($response); //DEBUG

  //lê o json que contem o time() do ultimo email enviado
  if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
  $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

  //Se na requisição para atualizar o produto houver problema (retorno dif de 200)
  // ele entra no bloco de código
  if($response["httpCode"] != 200)
  {
    $nome_funcao = "atualizaDescricaoMLB - SKU: $SKU - MLB: $MLB";
    $saida = $response['body']->message;
    $titulo = "Erro no Script Mercado Livre";
    mandaEmail_files_db($nome_funcao,$saida,$titulo);
  }
  else return 1;
}

function atualizaMLB($SKU,$MLB)
{
  $atualizaProd = atualizaProdMLB($SKU,$MLB);
  $atualizaDesc = atualizaDescricaoMLB($SKU,$MLB);

  if($atualizaProd && $atualizaDesc)
  {
    return '1';
  }
  else
  {
    return '0';
  }
}

function retorna_SKU($MLB)
{
  global $app_Id;
  global $secret_Key;
  global $DEBUG;


  $meli = new Meli($app_Id, $secret_Key);

  $params = array('attributes' => "attributes",
  'attributes&include_internal_attributes'=>"true");

  if(strpos($MLB, 'MLB') === 0) $MLB = substr($MLB, -10);

  $response = $meli->get('/items/MLB'.$MLB,$params);

  //lê o json que contem o time() do ultimo email enviado
  if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
  $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

  //Se na requisição para atualizar o produto houver problema (retorno dif de 200)
  // ele entra no bloco de código
  if($response['body'] == '') {
    echo "Este produto encontra-se para revisão devido nome/categoria do mesmo";
    return false;
  }
  if($response['httpCode'] != 200)
  {
    $nome_funcao = "retorna_SKU - MLB: $MLB";
    $saida = $response['body']->message;
    $titulo = "Erro no Script Mercado Livre";
    mandaEmail_files_db($nome_funcao,$saida,$titulo);
    return 0;
  }
  if($DEBUG) var_dump($response); //DEBUG

  foreach ($response['body']->attributes as $key => $value) {
    if($value->name == "Modelo") return $value->value_name;
  }
  //ESSE FOREACH procura pelo value Modelo, e retorna o modelo. se nao tiver, continua a execucao e retorna 0

  return 0;
}

function escreve_MLB($MLB)
{
  $conteudo_arquivo = file_put_contents("include/files/ultimo_MLB.json", json_encode($MLB));

  if(!$conteudo_arquivo)
  {
    return "0";
  }
  else
  {
    return "1";
  }
}

function retornaDadosVenda($COD){
  global $app_Id;
  global $secret_Key;
  global $DEBUG;

  $meli = new Meli($app_Id, $secret_Key);

  $params = array('access_token' => token()
);

//BLOCO PARA USAR AS ORDERS DE TESTE----
// global $DEBUG;
// $appId = "4946951783545211";
// $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
// $accesstoken = "APP_USR-4946951783545211-082816-9f29ea4048643e00d0ada759e51e7e93-327485416";
// $userid = '327485416';
//
// $meli = new Meli($appId, $secretKey);
//
// $params = array('access_token' => $accesstoken
// );
//--------------------------------------------

$response = $meli->get("/orders/$COD", $params);

if($DEBUG == true) echo "<h1>DEBUG retornaDadosVenda</h1><br>";
if($DEBUG == true) var_dump($response['body']); //DEBUG

$dadosVenda = new stdClass;

//------------PRODUTO--------
foreach ($response['body']->order_items as $key => $value) {
  $dadosVenda->mlb_produto = $value->item->id;
  $dadosVenda->sku_produto = retorna_SKU($dadosVenda->mlb_produto);
  $dadosVenda->nome_produto = $value->item->title;
  $dadosVenda->qtd_produto = $value->quantity;
  $dadosVenda->preco_unidade_produto = $value->unit_price;
  $dadosVenda->preco_total_produto = $value->full_unit_price;
}

//--------------PAGAMENTO---------
foreach ($response['body']->payments as $key => $value) {
  $dadosVenda->id_order = $value->order_id;
  $dadosVenda->date_created = strtotime($value->date_created);
  $dadosVenda->id_meio_pagamento = $value->payment_method_id;
  $dadosVenda->tipo_pagamento = $value->payment_type;
  $dadosVenda->custo_envio = $value->shipping_cost;
  $dadosVenda->total_pagar = $value->total_paid_amount;
  $dadosVenda->status_pagamento = $value->status;
}

//----- ------ENDEREÇO---------


if(!isset($response['body']->shipping->receiver_address)){
  $shipment_id = $response['body']->shipping->id;
  $params = array('access_token' => token()
);

$dados_shipping = $meli->get("/shipments/$shipment_id", $params);
// echo "<h1>AQUI ÒOOOOOO</h1>";
//   var_dump($response)    ;
$dadosVenda->id_shipping = $dados_shipping['body']->id;
$dadosVenda->rua = $dados_shipping['body']->receiver_address->street_name;
$dadosVenda->numero =$dados_shipping['body']->receiver_address->street_number;
$dadosVenda->bairro = $dados_shipping['body']->receiver_address->neighborhood->name;
$dadosVenda->cep = $dados_shipping['body']->receiver_address->zip_code;
$dadosVenda->cidade = $dados_shipping['body']->receiver_address->city->name;
$estado = $dados_shipping['body']->receiver_address->state->id;
$dadosVenda->estado = substr($estado,-2);
$dadosVenda->pais = $dados_shipping['body']->receiver_address->country->id;
}else{
  $dadosVenda->id_shipping = $response['body']->shipping->id;
  $dadosVenda->rua = $response['body']->shipping->receiver_address->street_name;
  $dadosVenda->numero =$response['body']->shipping->receiver_address->street_number;
  $dadosVenda->bairro = $response['body']->shipping->receiver_address->neighborhood->name;
  $dadosVenda->cep = $response['body']->shipping->receiver_address->zip_code;
  $dadosVenda->cidade = $response['body']->shipping->receiver_address->city->name;
  $estado = $response['body']->shipping->receiver_address->state->id;
  $dadosVenda->estado = substr($estado,-2);
  $dadosVenda->pais = $response['body']->shipping->receiver_address->country->id;
}

//PEGAR O ID DO PAIS -- COUNTRY_ID
// -------USUARIO --------
$dadosVenda->id_comprador = $response['body']->buyer->id;
$dadosVenda->apelido_comprador = $response['body']->buyer->nickname;
$dadosVenda->email_comprador = $response['body']->buyer->email;
$dadosVenda->cod_area_comprador = $response['body']->buyer->phone->area_code;
$dadosVenda->telefone_comprador = $response['body']->buyer->phone->number;
$dadosVenda->nome_comprador = "MLB-".ucwords(strtolower($response['body']->buyer->first_name));
$dadosVenda->sobrenome_comprador = ucwords(strtolower($response['body']->buyer->last_name));
$dadosVenda->tipo_documento_comprador = $response['body']->buyer->billing_info->doc_type;
$dadosVenda->numero_documento_comprador = $response['body']->buyer->billing_info->doc_number;

return $dadosVenda;

}

function retornaOrders(){
  global $app_Id;
  global $secret_Key;
  global $user_id;
  global $DEBUG;

  $meli = new Meli($app_Id, $secret_Key);

  $params = array('access_token' => token(),
  'seller' => $user_id, 'order.status' => "paid",
  'order.date_created.from' => "2018-08-10T00:00:00.000-00:00"
  );

  //BLOCO PARA USAR AS ORDERS DE TESTE----
  // global $DEBUG;
  // $appId = "4946951783545211";
  // $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
  // $accesstoken = "APP_USR-4946951783545211-082816-9f29ea4048643e00d0ada759e51e7e93-327485416";
  // $userid = '327485416';
  //
  // $meli = new Meli($appId, $secretKey);
  // $params = array('access_token' => $accesstoken,
  // 'seller' => $userid, 'order.status' => "paid",
  // 'order.date_created.from' => "2018-06-01T00:00:00.000-00:00"
  // );
  //--------------------------------------------------
  $response = $meli->get("/orders/search", $params);

  if($DEBUG == true) {echo "<h1>DEBUG retornaOrders</h1><br>"; var_dump($response['body']);}

  $idOrders = new stdClass;

  foreach ($response['body']->results as $key => $value)
  {
    $idOrders->$key = $value->payments[0]->order_id;
  }

  if(count($response['body']->results) < 1) return 0;

  return $idOrders;
}
/**
 * Funçao para auxiliar no workflow da função
 * Obtem a data e o id do comprador do pedido
 * Returns se foi possivel ou não
 *
 * @param string $orders_id    id do pedido do Mercado Livre
 *
 * @throws Exception
 *
 * @return string
 */
function retorna_data_pedidos($orders_id)
{
  $ml_data_pedido = new stdclass;
  foreach ($orders_id as $key => $value) {
    $dados_order = retornaDadosVenda($value);
    $ml_data_pedido->data_pedido[] = $dados_order->date_created;
    $ml_data_pedido->id_comprador[] = $dados_order->id_comprador;
  }
  $result_idbuyer =  file_put_contents("include/files/idbuyers.json", json_encode($ml_data_pedido->id_comprador));
  $result_data = file_put_contents("include/files/orderdate_create.json", json_encode($ml_data_pedido->data_pedido));

  if($result_data && $result_idbuyer) return "Ok"; else return "Não deu";
}

/**
 * Funçao que agrupa os pedidos do ML por comprador
 * Ao saber que um pedido é ligado a outro (compra com itens diferentes) o código os junta para ser uma compra somente
 * Returns o objeto criado :: Objeto->BuyerId->DadosDaCompra
 *
 * @throws Exception
 *
 * @return object
 */
function retornaDadosOrders()
{
  $orders = retornaOrders();
  if ($orders == 0) return 0;
  retorna_data_pedidos($orders);
  $magento_orders = new stdClass;
  foreach ($orders as $key => $value)
  {
    $dados_order = retornaDadosVenda($value);
    $lastdatecreate = json_decode(file_get_contents("include/files/orderdate_create.json"));
    $aux = $key+1;
    $aux1 = $key-1;
    $buyerid = json_decode(file_get_contents("include/files/idbuyers.json"));
    if(($buyerid[$aux1] == $dados_order->id_comprador)
    || ($buyerid[$aux] == $dados_order->id_comprador))
    {
      if ($aux == count($lastdatecreate)) $lastdatecreate[$aux] = time();
      if(($lastdatecreate[$aux] - $dados_order->date_created <= 2)
      || ($dados_order->date_created - $lastdatecreate[$aux1] <= 2))
      {
        $buyerid = "ID$dados_order->id_comprador";
        $magento_orders->$buyerid->id_order[] = $dados_order->id_order;
        $magento_orders->$buyerid->mlb_produto[] = $dados_order->mlb_produto;
        $magento_orders->$buyerid->sku_produto[] = $dados_order->sku_produto;
        $magento_orders->$buyerid->nome_produto[] = $dados_order->nome_produto;
        $magento_orders->$buyerid->qtd_produto[] = $dados_order->qtd_produto;

        $magento_orders->$buyerid->preco_unidade_produto[] = $dados_order->preco_unidade_produto;
        $magento_orders->$buyerid->preco_total_produto[] = $dados_order->preco_total_produto;

        $magento_orders->$buyerid->id_meio_pagamento = $dados_order->id_meio_pagamento;
        $magento_orders->$buyerid->tipo_pagamento = $dados_order->tipo_pagamento;
        $magento_orders->$buyerid->custo_envio = $dados_order->custo_envio;
        $magento_orders->$buyerid->total_pagar = $dados_order->total_pagar;
        $magento_orders->$buyerid->status_pagamento = $dados_order->status_pagamento;


        $magento_orders->$buyerid->id_shipping = $dados_order->id_shipping;
        $magento_orders->$buyerid->rua = $dados_order->rua;
        $magento_orders->$buyerid->numero = $dados_order->numero;
        $magento_orders->$buyerid->bairro = $dados_order->bairro;
        $magento_orders->$buyerid->cep = $dados_order->cep;
        $magento_orders->$buyerid->cidade = $dados_order->cidade;
        $magento_orders->$buyerid->estado = $dados_order->estado;
        $magento_orders->$buyerid->pais = $dados_order->pais;

        $magento_orders->$buyerid->id_comprador = $dados_order->id_comprador;
        $magento_orders->$buyerid->apelido_comprador = $dados_order->apelido_comprador;
        $magento_orders->$buyerid->email_comprador = $dados_order->email_comprador;
        $magento_orders->$buyerid->cod_area_comprador = $dados_order->cod_area_comprador;
        $magento_orders->$buyerid->telefone_comprador = $dados_order->cod_area_comprador.$dados_order->telefone_comprador;
        $magento_orders->$buyerid->nome_comprador = $dados_order->nome_comprador;
        $magento_orders->$buyerid->sobrenome_comprador = $dados_order->sobrenome_comprador;
        $magento_orders->$buyerid->tipo_documento_comprador = $dados_order->tipo_documento_comprador;
        $magento_orders->$buyerid->numero_documento_comprador = $dados_order->numero_documento_comprador;

      }
      else
      {
        $magento_orders->$key = $dados_order;
      }

    }
    else
    {
      $magento_orders->$key = $dados_order;
    }
  }

  return $magento_orders;
}

/**
 * Funçao para transformar o objeto retornado pela função retornaDadosOrders()
 * Converte o objeto da função retornaDadosOrders em um novo objeto para facilitar
 * Returns o objeto criado
 *
 * @throws Exception
 *
 * @return object
 */
function retornaObjMl($mlb)
{
  global $DEBUG;

  $dadosVenda = retornaDadosOrders();
  if($dadosVenda == '') return 0;

  $Magento_order = new stdClass();

  foreach($dadosVenda as $key => $value){
  if($mlb == $dadosVenda->$key->id_order)
  {
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
    $Magento_order->id_shipping = $dadosVenda->$key->id_shipping;
    $Magento_order->rua = $dadosVenda->$key->rua;
    $Magento_order->numero = $dadosVenda->$key->numero;
    $Magento_order->bairro = $dadosVenda->$key->bairro;
    $Magento_order->cep = $dadosVenda->$key->cep;
    $Magento_order->cidade = $dadosVenda->$key->cidade;
    $Magento_order->estado = $dadosVenda->$key->estado;
    $Magento_order->pais = $dadosVenda->$key->pais;

    // ---------USUARIO---------
    $Magento_order->id_comprador = $dadosVenda->$key->id_comprador;
    $Magento_order->apelido_comprador = $dadosVenda->$key->apelido_comprador;
    $Magento_order->email_comprador = $dadosVenda->$key->email_comprador;
    $Magento_order->cod_area_comprador = $dadosVenda->$key->cod_area_comprador;
    $Magento_order->telefone_comprador = $dadosVenda->$key->telefone_comprador;
    $Magento_order->nome_comprador = $dadosVenda->$key->nome_comprador;
    $Magento_order->sobrenome_comprador = $dadosVenda->$key->sobrenome_comprador;
    $Magento_order->tipo_documento_comprador = $dadosVenda->$key->tipo_documento_comprador;
    $Magento_order->numero_documento_comprador = $dadosVenda->$key->numero_documento_comprador;

  }
}
  return $Magento_order;
  if($DEBUG == TRUE) {echo "Estrutura do OBJ Magento_order";var_dump($Magento_order);}
}

function listaPedidoMLB()
{
  global $DEBUG;
  $Magento_order = retornaDadosOrders();

  if ($Magento_order == 0){ echo "Não há Novos pedidos"; return 0;}

  foreach ($Magento_order as $key => $value) {
    $json[] = $Magento_order->$key->id_order;

    $listaPedido = $json;
  }

  if (!isset($listaPedido)) return 0;
  $listagem = json_encode($listaPedido);

  if($DEBUG == true) var_dump($listagem);

  $conteudo_arquivo = file_put_contents("include/files/listaPedidoMLB.json", $listagem);

  if(!$conteudo_arquivo)
  {
    echo "Não deu pra escrever a lista de pedidos do mlb";
    return 0;
  }
  else
  {
    echo "Deu pra escrever a lista de pedidos do mlb";
    return true;
  }
}

function escrevePedidoMLB($MLB)

{
  $conteudo_arquivo = file_put_contents("include/files/ultimoPedidoMLB.json", json_encode($MLB));

  if(!$conteudo_arquivo)
  {
    return "Não deu pra escrever o pedido do mlb";
  }
  else
  {
    return "Escrito ultimo MLB com sucesso";
  }
}

function ultimoPedidoMLB()
{
  if(!file_exists("include/files/ultimoPedidoMLB.json")) return "Arquivo json não existente!";
  else {
    $conteudo_arquivo = file_get_contents("include/files/ultimoPedidoMLB.json");
    $retorno = $conteudo_arquivo;
    return $retorno;
  }
}

function proximoPedidoMLB()
{
  $ultimo = json_decode(ultimoPedidoMLB());
  $lista = file_get_contents("include/files/listaPedidoMLB.json");
  $lista = json_decode($lista);

  $indice_ultimo = array_search($ultimo, $lista);
  $indice_proximo = $indice_ultimo+1;

  $valor_proximo = $lista[$indice_proximo];
  $valor_zero = $lista["0"];

  if($indice_proximo+1 <= count($lista)) return $valor_proximo;
  else return $valor_zero;
}
/**
 * Funçao para controle dos pedidos ja existentes no Magento
 * Lê o json
 * Returns se a operação foi sucessida ou não
 *
 * @throws Exception
 *
 * @return string
 */
function retornaPedidosfeitosMGML()
{
  if(!file_exists("include/files/PedidosFeitosMLB.json"))
  {
    file_put_contents('include/files/PedidosFeitosMLB.json', "");
    return "Arquivo json não existente! Criado Novo arquivo";
  }
  else
  {
    $conteudo_arquivo = file_get_contents("include/files/PedidosFeitosMLB.json");
    $retorno = $conteudo_arquivo;
    return $retorno;
  }
}
/**
 * Funçao para controle dos pedidos ja existentes no Magento
 * Escreve no json o id do pedido criado no magento
 * Returns se a operação foi sucessida ou não
 *
 * @param string $mlb    id do pedido criado no magento
 *
 * @throws Exception
 *
 * @return string
 */
function escrevePedidoMGML($mlb)
{
  $listapedido = retornaPedidosfeitosMGML();
  $pos = strpos($listapedido, $mlb["0"]);

  if($pos == false)
  {
    $listapedido = (array) json_decode($listapedido);
    $listapedido[] =  array('MLB' => $mlb);
    $conteudo_arquivo = file_put_contents("include/files/PedidosFeitosMLB.json", json_encode($listapedido));

    if(!$conteudo_arquivo) echo "Não foi possível escrever/criar JSON com o pedido";
    else echo "Criado/escrito JSON pedido<br/>";
  }
}
/**
 * Cria a etiqueta da compra feita no mercado livre
 * Returns o nome do arquivo criado
 *
 * @param string $shipment_ids    id do envio (pode ser mais de um)
 * @param string $mlb             id do mercado livre
 * @param string $nome            nome do comprador
 * @param string $order_id        id do pedido no magento
 *
 * @return string
 */
function criaEtiqueta($shipment_ids, $mlb, $nome, $order_id)
{
  $token = token();
  $mlb = $mlb;
  $nome_arquivo = "etiquetas/$mlb-$order_id-$nome.pdf";
  $curl_url =  "https://api.mercadolibre.com/shipment_labels?shipment_ids=$shipment_ids&response_type=pdf&access_token=$token";
  $out = fopen($nome_arquivo,"w+");
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_FILE, $out);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_URL, $curl_url);
  curl_exec($ch);
  curl_close($ch);

  echo "Sucesso!!";

  return $nome_arquivo;
}

/**
 * Manda email a cada uma hora com todos os erros e apaga logo apos
 * Grava no json e no DB os erros durante uma hora
 * Returns 0 pois esta função é de erro
 *
 * @param string $nome_funcao    Nome da função em que houve o erro
 * @param string $saida          Saida de erro - o retorno da aplicação
 * @param string $titulo         Titulo do email
 *
 * @return string
 */
function mandaEmail_files_db($nome_funcao,$saida,$titulo){
//lê o json que contem o time() do ultimo email enviado
if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
$hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

  //estancia a classe com os parametros
  $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
  //Se o horario do json + 1 hora (3600 s) for menor ou igual ao horario
  //atual significa que ja passou uma hora e pode mandar novamente email
  if ($hora_email_enviado + 3600 <= time())
  {
    //estancia a função para criar a mensagem de corpo
    $error_handling->send_error_email();
    //estancia a função para executar as funções email()-db()-files() previamente
    //por padrão, as propriedades error_db e error_files estão true
    $error_handling->execute();
    //atualiza o json para a hora em que é mandado o email
    file_put_contents("include/files/ultimo_emailenviado.json", json_encode(time()));
    return "0";
  }
  else
  {
    //Caso não tenha dado uma hora do ultimo email enviado, é gravado
    //o erro no json de log  error_files/error_log.json
    //executa a função para criar a mensagem de erro
    $error_handling->send_errorlog_email();
    //executa a função para atualizar o json com o novo erro
    $error_handling->files();
    return "0";
  }

}

function testmail(){

  echo_debug('Iniciando testmail');
  $error_handling = new error_handling("Testando Email", "testEmail", 'Saida Teste', "teste");
  //estancia a função para criar a mensagem de corpo
  $error_handling->send_error_email();
  $error_handling->email();

  return "Enviado com Sucesso";

  }

function echo_debug($msg)
{
    echo date('r',time()).' - '.$msg.'<br>';

}

  function atualizaProdML($mlb)
  {
    global $app_Id;
    global $secret_Key;
    global $DEBUG;
    global $ajuste_preco_multiplicacao;
    global $ajuste_estoque;
    global $ajuste_preco_soma;
    global $sufixo_prod;
    global $prefixo_prod;
    global $marca;

    echo_debug('Iniciando atualizaProdML');

    if (is_null($mlb)) return "Campo MLB Vazio. Favor digitar MLB";

    $sku = retorna_SKU($mlb);
    if(!$sku) return "Este produto está com problemas";
    $produto = magento_product_summary($sku);

    if(!$produto) return "Não encontrado o produto $mlb no magento.";

    $title = $prefixo_prod.$produto['name'].$sufixo_prod;

    if (strlen($title) > 60) $title = $prefixo_prod.$produto['name'];

    $price = round(($produto['price'] * $ajuste_preco_multiplicacao)+$ajuste_preco_soma,2);
    $available_quantity = floor($produto['qty_in_stock'] + ($produto['qty_in_stock']*$ajuste_estoque));

    if ($DEBUG == true) var_dump($produto); //DEBUG

    if($available_quantity < 0) $available_quantity = 0;

    $meli = new Meli($app_Id, $secret_Key);

    $params = array('access_token' => token());

    $body = array
    (
      'title' => $title,
      'price' => $price,
      'available_quantity' => $available_quantity,
      'attributes' =>
      array(
        array('name' => "Marca",
        'value_name' => $marca),
        array('id' => "MODEL",
        'value_name' => $sku)
      )
    );

    $response = $meli->put("/items/MLB$mlb", $body, $params);

    var_dump($response); //DEBUG

    if($response['httpCode'] !== 200) return "Houve problemas com o produto $mlb";
    else return "Produto $mlb foi atualizado com sucesso!";
  }

function resumoProd($mlb){
  global $app_Id;
  global $secret_Key;
  global $DEBUG;

  echo_debug('Iniciando resumoProd');

  $meli = new Meli($app_Id, $secret_Key);
  $params = array('access_token' => token());

  $response = $meli->get("/items/MLB$mlb", $params);
  $array_produto;

  $array_produto['nome'] = $response['body']->title;
  $array_produto['preço'] = $response['body']->price;
  $array_produto['estoque'] = $response['body']->available_quantity;
foreach ($response['body']->attributes as $key => $value) {
  if($value->name == "Marca") $array_produto['marca'] = $value->value_name;
  if($value->name == "Modelo") $array_produto['SKU'] = $value->value_name;
}
  $array_produto['preço'] = $response['body']->price;

$response = $meli->get("/items/MLB$mlb/description", $params);
$array_produto['descrição'] = $response['body']->plain_text;

return $array_produto;
}

  function setarInicioLoop($mlb){

    echo_debug('Iniciando setarInicioLoop');

    if(is_null($mlb)) return "Campo MLB Vazio. Favor digitar MLB";
    //pega todos os produtos no ML
    $result = lista_MLB();

    $ml = "MLB$mlb";
    $indice_prod = array_search($ml, $result);

    if(!$indice_prod) return "Não encontrado o produto $mlb.";

    //seta no json o prod anterior ao sku passado por parametro
    file_put_contents('include/files/ultimo_MLB.json', substr($result[$indice_prod], -10));

    //
    return "Setado produto $mlb como proximo da lista do loop";
  }

  function listaProdMgt(){

    echo_debug('Iniciando listaProdMgt');
    $result = magento_catalogProductList();
    $array_skus = array();

     foreach ($result as $key => $value)
      $array_skus[] = array('SKU' => $value->sku,
                      'NOME' => $value->name);

    return $array_skus;
  }

  function listaProdML(){

    echo_debug('Iniciando listaProdML');

    global $app_Id;
    global $secret_Key;
    global $user_id;
    global $DEBUG;

    $meli = new Meli($app_Id, $secret_Key);
    $url = '/users/' . $user_id . '/items/search';
    $params = array(
      'access_token' => token(),
      'limit' => 100
    );

    $result = $meli->get($url, $params);

       $response = $meli->get("/items/$value", $params);

     return $response['body']->results;
  }
?>
