<?php
ini_set("error_reporting",E_ALL);
ini_set('display_errors', 1);
require_once 'include/all_include.php';

$sec_ini = time();

$resposta = $_GET["txtarea"];
$id = $_GET["id"];
if($resposta == '') {
  header('Location: perguntas_respostas.php?erro');
}
echo '<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<pre style="padding:5px;">';

$perg = new perguntas_respostas;

$resposta = $perg->respondePergunta($id, $resposta);
wait(5);
if($resposta) header('Location: perguntas_respostas.php?sucesso');
else header('Location: perguntas_respostas.php?problema');
?>
