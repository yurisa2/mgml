<?php
require 'include/all_include.php';
// 1 - Ler a lista de produtos no ML (arquivo list.php)
// 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)
// 3 - Descobrir o SKU através do MLB do próximo
// 4 - Rodar a função de atualização com os dois dados
// 5 - Escrever o ultimo no json
//
// $lista_produto = lista_MLB();
$time_inicial = time();
echo "<pre><br>";
echo "TEMPO: ". (time() - $time_inicial);
echo "<br>";

echo "<h2>1 - Ler a lista de produtos no ML (arquivo list.php)  -- IMPLICITO NO PROXIMO PASSO</h2>";
echo "<br>";

echo "<h2>2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)</h2>";
echo "<br>";
$MLB = proximo_MLB(); // 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)
echo "<h2>MLB: $MLB <BR>";
echo "TEMPO:". (time() - $time_inicial);
echo "<br><br><br></h2>";

echo "<h2>3 - Descobrir o SKU através do MLB do próximo</h2>";
$SKU = retorna_SKU($MLB); // 3 - Descobrir o SKU através do MLB do próximo
echo "<br>";
echo "<h2>SKU: $SKU <BR>";
echo "TEMPO: ". (time() - $time_inicial);
echo "<br><br><br></h2>";

if(isset($MLB) && isset($SKU))
{
echo "<h2>4 - Rodar a função de atualização com os dois dados</h2>";
echo "<br>";
$atualiza = atualizaMLB($SKU,$MLB); // 4 - Rodar a função de atualização com os dois dados
echo "<h2>atualiza: $atualiza <BR>";
}
echo "TEMPO: ". (time() - $time_inicial);


if($atualiza)
{
echo "<h2>5 - Escrever o ultimo no json</h2>";
echo "<br>";
$escreveMLB = escreve_MLB($MLB); // 5 - Escrever o ultimo no json
echo "<h2>escreveMLB: $escreveMLB <BR>";
}
echo "TEMPO: ". (time() - $time_inicial);
echo "<br><br><br></h2>";
