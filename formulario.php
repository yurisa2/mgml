<?php if (isset($_GET['mlb'])) {
  require 'include/all_include.php';
  $MLB = $_GET['mlb'];
  if ($_GET['debugar'] == 'true') $DEBUG = true;

  $time_inicial = time();
  echo "<pre><br>";
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br>";

  echo "<h2>1 - Ler a lista de produtos no ML (arquivo list.php)  -- IMPLICITO NO PROXIMO PASSO</h2>";
  echo "<br>";

  echo "<h2>2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)</h2>";
  echo "<br>";
  $MLB = proximo_MLB(); // 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)
  echo "<h2>ATUAL MLB: ". ultimo_MLB() ."<BR>PROXIMO MLB: $MLB <BR>";
  echo "TEMPO:". (time() - $time_inicial);
  echo "<br><br><br></h2>";

  echo "<h2>3 - Descobrir o SKU através do MLB do próximo</h2>";
  $SKU = retorna_SKU($MLB); // 3 - Descobrir o SKU através do MLB do próximo
  echo "<br>";
  echo "<h2>ATUAL SKU:". retorna_SKU(ultimo_MLB()). "<BR>PROXIMO SKU: $SKU <BR>";
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br></h2>";

  if((isset($MLB) && isset($SKU)) && ($MLB != 0 && $SKU != 0))
  {
    echo "<h2>4 - Rodar a função de atualização com os dois dados</h2>";
    echo "<br>";
    $atualiza = atualizaMLB($SKU,$MLB); // 4 - Rodar a função de atualização com os dois dados
    echo "<h2>atualiza: $atualiza <BR>";
  }
  echo "TEMPO: ". (time() - $time_inicial);

  // if($atualiza)
  // {
    echo "<h2>5 - Escrever o ultimo no json</h2>";
    echo "<br>";
    $escreveMLB = escreve_MLB($MLB); // 5 - Escrever o ultimo no json
    echo "<h2>escreveMLB: $escreveMLB <BR>";
  //}
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br></h2>";

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
