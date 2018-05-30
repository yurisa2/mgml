<?php
session_start();

require 'include/config.php';

$meli = new Meli($appId, $secretKey);

$items = $meli->post('/items', $item, array('access_token' => $_SESSION['access_token']));


var_dump($items);
