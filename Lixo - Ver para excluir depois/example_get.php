<?php
require 'include/ml/php-sdk/Meli/meli.php';
require 'include/ml/php-sdk/configApp.php';

$appId = "2250386536833494";
$secretKey = "rUQOUH0NEbWcYugufRE3mHLKoHnR0IED";

$meli = new Meli($appId, $secretKey);

$params = array();

$url = '/sites/' . $siteId;

$result = $meli->get($url, $params);

echo '<pre>';
print_r($result);
echo '</pre>';
