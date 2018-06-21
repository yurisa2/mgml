<?php

function token()
{
  $variavel = json_decode(file_get_contents("include/files/tokens.json"));
  $access_token = $variavel->access_token;
  $refresh_token = $variavel->refresh_token;

  if(time() > $variavel->time)
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
  $token_info["time"] = time()+10000;
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
  $ultimo = ultimo_MLB();
  $ultimo = "MLB".$ultimo;
  $lista = lista_MLB();

  $indice_ultimo = array_search($ultimo, $lista);
  $indice_proximo = $indice_ultimo+1;

  $valor_proximo = substr($lista[$indice_proximo], 3);
  $valor_zero = substr($lista["0"], 3);


  if($indice_proximo+1 == count($lista)) return $valor_zero;
  else return $valor_proximo;
}

function atualizaProdMLB($SKU,$MLB)
{
  global $app_Id;
  global $secret_Key;
  global $DEBUG;
  global $ajuste_preco;
  global $sufixo_prod;
  global $prefixo_prod;
  global $marca;


  $produto = magento_product_summary($SKU);

  if(!$produto) return 0;

  $title = $prefixo_prod.$produto['name'].$sufixo_prod;
  //CRIAR TESTES PARA O SUFIXO
  $price = round($produto['price'] * $ajuste_preco,2);
  $available_quantity = $produto['qty_in_stock'];

if ($DEBUG == true) var_dump($title); //DEBUG

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
        array(
          'name' => "Marca",
          'value_name' => $marca),
// DEBUG AQUI PRECISA TER O SKU CASO CONTRARIO ELE ESCREVE A MARCA E ANULA  $SKU
//PROVAVELMENTE ESTARÃO SEM SKU ALGUNS DOS ANUNCIOS
         array(
           'id' => "MODEL",
           'value_name' => $SKU)
        )
  );


  $response = $meli->put('/items/MLB'.$MLB, $body, $params);

  // echo "body: <br> "; var_dump($body); //DEBUG
  //
  // echo "response: <br> "; var_dump($response); //DEBUG

  // echo "MLB: $MLB";
  if ($DEBUG == true) var_dump($response); //DEBUG

  if($response["httpCode"] == 200)
  {
    return "1";
  }
  else
  {
    return "0";
  }
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

  if($response["httpCode"] == 200)
  {
    return "1";
  }
  else
  {
    return "0";
  }
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

  // echo "<pre>";
  // var_dump($response['body']->attributes); //DEBUG
  if ($DEBUG == true) var_dump($response); //DEBUG


  //LUIGI, aqui precisei fazer isso pois voce assumiu que o SKU estaria sempre no indice 2 (o que nao é verdade)
  foreach ($response['body']->attributes as $key => $value) {
    if($value->name == "Modelo") return $value->value_name;
    // echo "<br>";
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

  $response = $meli->get("/orders/$COD", $params);

  echo "<pre>";

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
  $dadosVenda->id_order = $value->id;
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
    $dadosVenda->estado = $dados_shipping['body']->receiver_address->state->name;
    $dadosVenda->pais = $dados_shipping['body']->receiver_address->country->name;
  }else{
    $dadosVenda->id_shipping = $response['body']->shipping->id;
    $dadosVenda->rua = $response['body']->shipping->receiver_address->street_name;
    $dadosVenda->numero =$response['body']->shipping->receiver_address->street_number;
    $dadosVenda->bairro = $response['body']->shipping->receiver_address->neighborhood->name;
    $dadosVenda->cep = $response['body']->shipping->receiver_address->zip_code;
    $dadosVenda->cidade = $response['body']->shipping->receiver_address->city->name;
    $dadosVenda->estado = $response['body']->shipping->receiver_address->state->name;
    $dadosVenda->pais = $response['body']->shipping->receiver_address->country->name;
  }

//PEGAR O ID DO PAIS -- COUNTRY_ID
  // -------USUARIO --------
  $dadosVenda->id_comprador = $response['body']->buyer->id;
  $dadosVenda->apelido_comprador = $response['body']->buyer->nickname;
  $dadosVenda->email_comprador = $response['body']->buyer->email;
  $dadosVenda->cod_area_comprador = $response['body']->buyer->phone->area_code;
  $dadosVenda->telefone_comprador = $response['body']->buyer->phone->number;
  $dadosVenda->nome_comprador = $response['body']->buyer->first_name;
  $dadosVenda->sobrenome_comprador = $response['body']->buyer->last_name;
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
  'seller' => $user_id, 'order.status' => "paid");

  // $params = array('access_token' => $accesstoken,
  // 'seller' => "327485416",
  // 'order.date_created.from' => "2018-06-11T00:00:00.000-00:00",
  // 'order.date_created.to' => "2018-06-13T00:00:00.000-00:00"
  // );
  $response = $meli->get("/orders/search", $params);
  if($DEBUG == true) var_dump($response);

  $idOrders = new stdClass;

  foreach ($response['body']->results as $key => $value) {
    $i = "order_id_".$key;
    $idOrders->$i = $value->payments[0]->order_id;

  }
  return $idOrders;
}
?>
