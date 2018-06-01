<?php
session_start();

require 'include/all_include.php';

$meli = new Meli($appId, $secretKey);

$params = array(
'access_token' => $access_token,
'grant_type' => 'refresh_token'



);

$item = array();

$items = $meli->post('/oauth/token', $item, $params);

echo '<pre>';
var_dump($items);
