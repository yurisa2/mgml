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

  // var_dump($result); //DEBUG

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

  $produto = magento_product_summary($SKU);
  $title = $produto['name'];
  $price = round($produto['price'],2);
  $available_quantity = $produto['qty_in_stock'];

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
          'value_name' => "Easypath"),
          array(
            'id' => "MODEL",
            'value_name' => $SKU)
          ),
          array(
            'warranty' => "Garantia de 3 meses")
  );

  $response = $meli->put('/items/MLB'.$MLB, $body, $params);

  // echo "body: <br> "; var_dump($body); //DEBUG
  //
  // echo "response: <br> "; var_dump($response); //DEBUG

  // echo "MLB: $MLB";


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

  $produto = magento_product_summary($SKU);

  $description = $produto['description'];

  $meli = new Meli($app_Id, $secret_Key);

  $params = array('access_token' => token());

  $body = array
  (
    'plain_text' => $description
  );

  $response = $meli->put('/items/MLB'.$MLB.'/description', $body, $params);

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

  if(!$atualizaProd and !$atualizaDesc)
  {
    return '0';
  }
  else
  {
    return '1';
  }
}

function retorna_SKU($MLB)
{
  global $app_Id;
  global $secret_Key;

  $meli = new Meli($app_Id, $secret_Key);

  $params = array('attributes' => "attributes",
  'attributes&include_internal_attributes'=>"true");

  $response = $meli->get('/items/MLB'.$MLB,$params);

  // echo "<pre>";
  // var_dump($response['body']->attributes); //DEBUG

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

?>
