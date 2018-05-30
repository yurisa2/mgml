<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("soap.wsdl_cache_enabled","1");
ini_set('xdebug.var_display_max_depth', 5);
ini_set('xdebug.var_display_max_children', 350);
ini_set('xdebug.var_display_max_data', 1024);

require_once 'include/all_include.php';

echo '<pre>';

// magento_info();

// echo magento_session();

// magento_catalogProductList();


//var_dump(magento_catalogProductInfo('EP-51-35051'));

// var_dump(magento_catalogProductInfo_description('EP-51-35051'));


// var_dump(magento_catalogInventoryStockItemList('EP-51-35051'));

$produto = magento_product_summary('EP-51-60116');

var_dump($produto);

// var_dump(magento_catalogInventoryStockItemUpdate('EP-51-35051','665'));

?>
