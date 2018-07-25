<?php
require 'include/all_include.php';

$meli = new Meli($appId, $secretKey);

$params = array();

$url = '/items/MLB770723406?access_token=' . $ACCESS_TOKEN;

$result = $meli->get($url, $params);

$atributos = $result["body"]->attributes;

echo '<pre>';
var_dump($result["body"]);



echo '</pre>';
