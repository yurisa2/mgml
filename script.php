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
