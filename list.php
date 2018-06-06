<?php
require 'include/all_include.php';

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
echo '<pre>';
var_dump(lista_MLB());
