<?php
include "include/all_include.php";
$media_de_cada_passe = 0;
$i=1;
do {

  $tempo = time();


  echo "QTD de vezes: ".$i;

  echo "";


   echo "<br>";
   echo "TEMPO: ". (time() - $tempo);
   echo "<br><br><br></h2>";
 $tempo_ate_agora = (time() - $tempo);
 $media_de_cada_passe = ($media_de_cada_passe + $tempo_ate_agora)/2;


 echo "<br>Media de cada lance: ".$media_de_cada_passe;
 $i++;
}
while (($tempo_ate_agora + $media_de_cada_passe) < 0);

// do
// {
//   $time_inicial = time();
//   echo "<pre><br>";
//   echo "TEMPO: ". (time() - $time_inicial);
//   echo "<br>";
//
//   echo "<h2>1 - Ler a lista de produtos no ML (arquivo list.php)  -- IMPLICITO NO PROXIMO PASSO</h2>";
//   echo "<br>";
//
//   echo "<h2>2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)</h2>";
//   echo "<br>";
//   $MLB = proximo_MLB(); // 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)
//   $next_MLB = ultimo_MLB();
//   echo "<h2>ATUAL MLB: $MLB<BR>ANTERIOR MLB: $next_MLB  <BR>";
//   echo "TEMPO:". (time() - $time_inicial);
//   echo "<br><br><br></h2>";
//
//   echo "<h2>3 - Descobrir o SKU através do MLB do próximo</h2>";
//   $SKU = retorna_SKU($MLB); // 3 - Descobrir o SKU através do MLB do próximo
//   echo "<br>";
//   echo "<h2>ATUAL SKU: ". $SKU ."<BR>PROXIMO SKU:". retorna_SKU($next_MLB) ."<BR>";
//   echo "TEMPO: ". (time() - $time_inicial);
//   echo "<br><br><br></h2>";
//
//   if((isset($MLB) && isset($SKU)) || ($MLB != 0 && $SKU != 0))
//   {
//     echo "<h2>4 - Rodar a função de atualização com os dois dados</h2>";
//     echo "<br>";
//     $atualiza = atualizaMLB($SKU,$MLB); // 4 - Rodar a função de atualização com os dois dados
//     echo "<h2>atualiza: $atualiza <BR>";
//   }
//   echo "TEMPO: ". (time() - $time_inicial);
//   echo "<br><br><br></h2>";
//   // if($atualiza)
//   // {
//     echo "<h2>5 - Escrever o ultimo MLB atualizado no json</h2>";
//     echo "<br>";
//     $escreveMLB = escreve_MLB($MLB); // 5 - Escrever o ultimo no json
//     echo "<h2>escreveMLB: $escreveMLB <BR>";
//   // }
//
//   echo "TEMPO: ". (time() - $time_inicial);
//   $tempo_até_agora = (time() - $time_inicial);
//   $média_de_cada_passe = $média_de_cada_passe+$tempo_até_agora;
//
//   echo "<br><br><br></h2>";
// }
// while (($tempo_até_agora + $média_de_cada_passe) < 60);

// USUARIO DE TESTES
//  ["id"]=> int(327485416)
//  ["nickname"]=> string(8) "TT784263"
//  ["password"]=> string(10) "qatest7896"
//  ["site_status"]=> string(6) "active"
//  ["email"]=> string(31) "test_user_97680688@testuser.com" }
//
//USUARIO DE TESTES 2
// ["id"]=> int(327509935)
//     ["nickname"]=> string(12) "TEST4CXNCJNZ"
//     ["password"]=> string(10) "qatest8331"
//     ["site_status"]=> string(6) "active"
//     ["email"]=> string(30) "test_user_2645635@testuser.com"
