<?php
include "include/all_include.php";



function retornaDadosVenda($COD){
  $appId = "4946951783545211";
  $secretkey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
  $userId = "327485416";
  $accesstoken = "APP_USR-4946951783545211-061115-2958ee93c3baa91f1957ed50df0c2574-327485416";

  global $DEBUG;

  $meli = new Meli($appId, $secretkey);

  $params = array('access_token' => $accesstoken
  );

  //1047551434

  // $response = $meli->post('/users/test_user', $body, $params);
  //https://api.mercadolibre.com/orders/search?seller=seller_id&order.status=paid&access_token=
  $response = $meli->get("/orders/$COD", $params);

  echo "<pre>";

  //if ($DEBUG == true) var_dump($response); //DEBUG

  $dadosVenda = new stdClass;

  //------------PRODUTO--------
  foreach ($response['body']->order_items as $key => $value) {
    $dadosVenda->id = $value->item->id;
    $dadosVenda->title = $value->item->title;
    $dadosVenda->quantity = $value->quantity;
    $dadosVenda->unit_price = $value->unit_price;
    $dadosVenda->full_unit_price = $value->full_unit_price;
  }

  //--------------PAGAMENTO---------
  foreach ($response['body']->payments as $key => $value) {
  $dadosVenda->payment_method_id = $value->payment_method_id;
  $dadosVenda->payment_type = $value->payment_type;
  $dadosVenda->shipping_cost = $value->shipping_cost;
  $dadosVenda->total_paid_amount = $value->total_paid_amount;
  $dadosVenda->status = $value->status;
  }

  //----- ------ENDEREÇO---------
  $dadosVenda->id = $response['body']->shipping->receiver_address->id;
  $dadosVenda->street_name = $response['body']->shipping->receiver_address->street_name;
  $dadosVenda->street_number =$response['body']->shipping->receiver_address->street_number;
  $dadosVenda->neighborhood = $response['body']->shipping->receiver_address->neighborhood->name;
  $dadosVenda->cep = $response['body']->shipping->receiver_address->zip_code;
  $dadosVenda->city = $response['body']->shipping->receiver_address->city->name;
  $dadosVenda->state = $response['body']->shipping->receiver_address->state->name;
  $dadosVenda->country = $response['body']->shipping->receiver_address->country->name;

  // -------USUARIO --------
  $dadosVenda->id = $response['body']->buyer->id;
  $dadosVenda->nickname = $response['body']->buyer->nickname;
  $dadosVenda->email = $response['body']->buyer->email;
  $dadosVenda->area_code = $response['body']->buyer->phone->area_code;
  $dadosVenda->phone = $response['body']->buyer->phone->number;
  $dadosVenda->first_name = $response['body']->buyer->first_name;
  $dadosVenda->last_name = $response['body']->buyer->last_name;
  $dadosVenda->doc_type = $response['body']->buyer->billing_info->doc_type;
  $dadosVenda->doc_number = $response['body']->buyer->billing_info->doc_number;


  return $dadosVenda;

}


var_dump(retornaDadosVenda('1731250297'));
