<?php
require 'include/config.php';

$meli = new Meli($appId, $secretKey);
echo '<pre>';
$url = '/users/' . $user_id . '/items/search';

$params = array(
  'access_token' => $access_token,
  'limit' => 100
);
$result = $meli->get($url, $params);
$limit = $result['body']->limit;

if($result['body']->total > $result['body']->limit)
{
  //Mandar e mail e o caralho pq nao fiz paginação (preguiça né)
  exit;

}
//
// $paginas = ((integer)($result['body']->paging->total / $result['body']->paging->limit)+1);
// //Até aqui era só pra pegar as paginas

$listagem = $result['body']->results;
  $listagem = array_unique($listagem);


var_dump($listagem);



print_r($result);
echo '</pre>';
