<?php
require 'include/config.php';

$meli = new Meli($appId, $secretKey);

$params = array();

$url = '/users/' . $user_id . '?access_token=' . $ACCESS_TOKEN;

$result = $meli->get($url, $params);

echo '<pre>';
print_r($result);
echo '</pre>';
