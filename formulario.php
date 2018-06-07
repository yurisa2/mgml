<?php if (isset($_GET['mlb'])) {
  require 'include/all_include.php';
  $MLB = $_GET['mlb'];
  if ($_GET['debugar'] == 'true') $DEBUG = true;

  $time_inicial = time();
  echo "<pre><br>";
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br>";

  echo "1 - Ler a lista de produtos no ML (arquivo list.php)  -- IMPLICITO NO PROXIMO PASSO";
  echo "<br>";

  echo "2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)";
  echo "<br>";
  $MLB = proximo_MLB(); // 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)
  echo "MLB: $MLB <BR>";
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br>";

  echo "3 - Descobrir o SKU através do MLB do próximo";
  $SKU = retorna_SKU($MLB); // 3 - Descobrir o SKU através do MLB do próximo
  echo "<br>";
  echo "SKU: $SKU <BR>";
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br>";

  if(isset($MLB) && isset($SKU))
  {
  echo "4 - Rodar a função de atualização com os dois dados";
  echo "<br>";
  $atualiza = atualizaMLB($SKU,$MLB); // 4 - Rodar a função de atualização com os dois dados
  echo "atualiza: $atualiza <BR>";
  }
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br>";

  if($atualiza)
  {
  echo "5 - Escrever o ultimo no json";
  echo "<br>";
  $escreveMLB = escreve_MLB($MLB); // 5 - Escrever o ultimo no json
  echo "escreveMLB: $escreveMLB <BR>";
  }
  echo "TEMPO: ". (time() - $time_inicial);

}else {?>
<html>
<head>
  <meta charset="utf-8">
</head>
  <body>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" name="form" method="get">
      <label>Código MLB</label><br>
      <input type="text" placeholder="código mlb" name="mlb"><br>
      <label>Debugar</label><br>
      <input type="radio" name="debugar" value="true">Sim<br>
      <input type="radio" name="debugar" value="false"> Nao<br>
      <button type="submit" value="Enviar">Enviarrrr</button>
    </form>
  </body>

</html>
<?php } ?>
