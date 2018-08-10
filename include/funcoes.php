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

  if($result["httpCode"] != 200)
  {
    $nome_funcao = "lista_MLB";
    $saida = serialize($result);
    $titulo = "Erro de credendial no Script Mercado Livre";
    $tipo = "Erro";
    $error_handling = new error_handling($titulo, $nome_funcao, $saida, $tipo);
    $error_handling->send_error_email();
    $error_handling->execute();
    return "0";
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

  if(!$produto) return 0;
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

  if($response["httpCode"] == 200) return "1";
  else
  {
    $nome_funcao = "atualizaProdMLB";
    $saida = serialize($response);
    $titulo = "Erro no Script Mercado Livre";
    $tipo = "Erro";
    $error_handling = new error_handling($titulo, $nome_funcao, $saida, $tipo);
    $error_handling->send_error_email();
    $error_handling->execute();
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

  if($response["httpCode"] == 200) return "1";
  else
  {
    $nome_funcao = "atualizaDescricaoMLB";
    $saida = serialize($response);
    $titulo = "Erro no Script Mercado Livre";
    $tipo = "Erro";
    $error_handling = new error_handling($titulo, $nome_funcao, $saida, $tipo);
    $error_handling->send_error_email();
    $error_handling->execute();
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

  if(!$response){
    $error = "retorna_SKU";
    $debug = serialize($response);
    $corpo = send_error_email($error, $debug);
    $assunto = "Erro no Script Mercado Livre";
    manda_mail($assunto, $corpo);
  }
  if ($DEBUG == true) var_dump($response['body']); //DEBUG


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

//BLOCO PARA USAR AS ORDERS DE TESTE----
  // global $DEBUG;
  // $appId = "4946951783545211";
  // $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
  // $accesstoken = "APP_USR-4946951783545211-080213-3d82febc4de927d0be2631577ab082f8-327485416";
  // $userid = '327485416';
  //
  // $meli = new Meli($appId, $secretKey);
  //
  // $params = array('access_token' => $accesstoken
  // );
//--------------------------------------------

  $response = $meli->get("/orders/$COD", $params);

  // echo "<pre><h1>Aqui</h1>";

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
  $dadosVenda->nome_comprador = "MLB-".$response['body']->buyer->first_name;
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
    'seller' => $user_id, 'order.status' => "paid",
     'order.date_created.from' => "2018-08-02T00:00:00.000-00:00"
  );

//BLOCO PARA USAR AS ORDERS DE TESTE----
  // global $DEBUG;
  // $appId = "4946951783545211";
  // $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
  // $accesstoken = "APP_USR-4946951783545211-080213-3d82febc4de927d0be2631577ab082f8-327485416";
  // $userid = '327485416';
  //
  // $meli = new Meli($appId, $secretKey);
  // $params = array('access_token' => $accesstoken,
  // 'seller' => $userid, 'order.status' => "paid");
  // $params = array('access_token' => $accesstoken,
  // 'seller' => $userid, 'order.status' => "paid",
  // 'order.date_created.from' => "2018-06-12T00:00:00.000-00:00",
  // 'order.date_created.to' => "2018-06-13T00:00:00.000-00:00"
// );
//--------------------------------------------------
  $response = $meli->get("/orders/search", $params);
    var_dump($response);
  if($DEBUG == true) {echo "<h1>DEBUG retornaOrders</h1><br>"; var_dump($response['body']->results);}

  $idOrders = new stdClass;

  foreach ($response['body']->results as $key => $value) {
    // $i = "order_id_".$key;
    $idOrders->$key = $value->payments[0]->order_id;

  }
  return $idOrders;
}
//LEMBRAR DE ARRUMAR CAMPO SKU E O RESTO DESTE ARQUIVO
function retornaDadosOrders()
{
  $orders = retornaOrders();
  // $sku_debug = "EP-51-40971";//$dados_order->sku_produto;
  $magento_orders = new stdClass;
  foreach ($orders as $key => $value) {
    $dados_order = retornaDadosVenda($value);

    $buyerid = $dados_order->id_comprador;
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
  return $magento_orders;
}

function retornaObjMl()
{
  global $DEBUG;

  $dadosVenda = retornaDadosOrders();
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
  return $Magento_order;
  if($DEBUG == TRUE) {echo "Estrutura do OBJ Magento_order";var_dump($Magento_order);}
}

function listaPedidoMLB()
{
  global $DEBUG;
  $Magento_order = retornaDadosOrders();

  foreach ($Magento_order as $key => $value) {
    $json[] = $Magento_order->$key->id_order;

    $listaPedido = $json;
  }
  $listagem = json_encode($listaPedido);

  $conteudo_arquivo = file_put_contents("include/files/listaPedidoMLB.json", $listagem);

  if(!$conteudo_arquivo) return "Não deu pra escrever a lista de pedidos do mlb";
  else return "Deu pra escrever a lista de pedidos do mlb";
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

  if($indice_proximo+1 < count($lista)) return $valor_proximo;
  else return $valor_zero;
}

function retornaPedidosfeitosMGML()
{
  if(!file_exists("include/files/PedidosFeitosMLB.json"))
  {
    file_put_contents('include/files/PedidosFeitosMLB.json', "");
    return "Arquivo json não existente! Criado Novo arquivo";
  }
  else {
    $conteudo_arquivo = file_get_contents("include/files/PedidosFeitosMLB.json");
    $retorno = $conteudo_arquivo;
    return $retorno;
  }
}

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

?>
