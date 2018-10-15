<?php
class perguntas_respostas
{
  function retorna_idPerguntas()
  {
    global $app_Id;
    global $secret_Key;
    global $user_id;

    $meli = new Meli($app_Id, $secret_Key);
    $params = array('seller_id' => $user_id, 'access_token' => token(), 'status' => "UNANSWERED");

    $response = $meli->get('/questions/search', $params);
    if(count($response['body']->questions) == 0) return false;

    foreach ($response['body']->questions as $key => $value)
    {
        $lista_perguntas[] = $value->id;
    }
    $response = file_put_contents('include/perg_resp/lista_id_perguntas.json', json_encode($lista_perguntas));
    if(!$response) return "ERRO AO GRAVAR INFORMAÇÕES NO JSON";
    return $lista_perguntas;
  }

  function retorna_pergunta($id_pergunta)
  {
    global $app_Id;
    global $secret_Key;
    global $user_id;

    $meli = new Meli($app_Id, $secret_Key);
    $params = array('access_token' => token());

    foreach ($id_pergunta as $key => $value)
    {
      $response = $meli->get("/questions/$value", $params);

      $mlb = $response['body']->item_id;
      $response2 = $meli->get("/items/$mlb", $params);

      $dados_pergunta = json_decode(file_get_contents('include/perg_resp/perguntas_ativas.json'),true);
      $dados_pergunta = array('ID' => $response['body']->id,
                             'PERGUNTA' => $response['body']->text,
                             'PRODUTO' => $response2['body']->title);
      $dados_perguntas[] = $dados_pergunta;
    }
    $response = file_put_contents('include/perg_resp/perguntas_ativas.json', json_encode($dados_perguntas,JSON_UNESCAPED_UNICODE));
    if(!$response) return "ERRO AO GRAVAR INFORMAÇÕES NO JSON";

    return $dados_perguntas;
  }

  function ultimaPergunta()
  {
    if(!file_exists("include/files/ultimaPerg.json")) file_put_contents("include/perg_resp/ultimaPerg.json", "");
    else {
      $conteudo_arquivo = file_get_contents("include/perg_resp/ultimaPerg.json");
      $retorno = $conteudo_arquivo;
      return $retorno;
    }
  }

  function proximaIdPergunta()
  {
    $ultimo = json_decode($this->ultimaPergunta());
    $lista = file_get_contents("include/perg_resp/lista_id_perguntas.json");
    $lista = json_decode($lista);

    $indice_ultimo = array_search($ultimo, $lista);
    $indice_proximo = $indice_ultimo+1;
    if($indice_proximo < count($lista)) $valor_proximo = $lista[$indice_proximo];

    $valor_zero = $lista["0"];

    if($indice_proximo+1 <= count($lista)) return $valor_proximo;
    else return $valor_zero;
  }

  function proximaPergunta($id_pergunta)
  {
    $lista = json_decode(file_get_contents("include/perg_resp/perguntas_ativas.json"));

    foreach ($lista as $key => $value) {
      if($value->ID == $id_pergunta) return $lista[$key];
    }

    return "Não encontrado";
  }

  function respondePergunta($id_pergunta, $text)
  {
    global $app_Id;
    global $secret_Key;
    global $user_id;

    $meli = new Meli($app_Id, $secret_Key);
    $params = array('access_token' => token());

    $body = array('question_id' => $id_pergunta,'text' => $text);

    $response = $meli->post("/answers", $body, $params);

    if($response['httpCode'] != 200) return "Erro ao tentar responder a pergunta $id_pergunta";

    $dados_pergunta = json_decode(file_get_contents('include/perg_resp/perguntasrespondidas.json'), true);
    $dados_pergunta[] = array('ID'=>$id_pergunta,'RESPOSTA'=>$text);
    $response = file_put_contents('include/perg_resp/perguntasrespondidas.json', json_encode($dados_pergunta,JSON_UNESCAPED_UNICODE));

    if ($response)
    {
      $this->escreveultimaPergunta($id_pergunta);
      return true;
    }
  }

  function escreveultimaPergunta($id_pergunta)
  {
    file_put_contents("include/perg_resp/ultimaPerg.json", json_encode($id_pergunta));

  }
}
 ?>
