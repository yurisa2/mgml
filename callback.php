<?php

echo "<pre>_GET<br>";



$code = file_put_contents("code.json", json_encode($_GET));
var_dump($code);
echo "<pre>_POST<br>";

var_dump($_POST);
 ?>
