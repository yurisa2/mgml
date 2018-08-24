<?php
ini_set("error_reporting",E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "include/all_include.php";
$DEBUG = false;
echo "<pre>";

  // httpcode = 403
//2018-06-16T19:01:42.000-04:00
//2018-06-16T19:01:42.000-04:00
// if('2018-06-16T19:01:42.000-04:00' - '2018-06-16T19:04:42.000-04:00' > 1)
//echo 'Sim'; else echo "Nao";

// $r = retornaOrders();
// foreach ($r as $key => $value) {
//   var_dump($r);
//   var_dump(retornaDadosVenda($value));
// }

echo "fffffffffffffffffffff";
var_dump(retornaDadosOrders());

// if(substr($s,0,10) == substr($r,0,10)) {
//   echo "teste data";
//   if(substr($s,-18,2) == substr($r,-18,2)) {
//     echo "teste hora";
//   if(substr($s,-15, 2) - substr($r,-15, 2) < 1){
//     echo "teste minuto";
//     if(substr($s,-12, 2) - substr($r,-12, 2) < 2) {
//             echo "teste segundo";
//       $magento_orders->$buyerid->id_order = 11;
//       $magento_orders->$buyerid->mlb_produto = 11;
//       $magento_orders->$buyerid->sku_produto = 11;
//       $magento_orders->$buyerid->nome_produto = 11;
//       $magento_orders->$buyerid->qtd_produto = 11;
//       $magento_orders->$buyerid->preco_unidade_produto = 11;
//       $magento_orders->$buyerid->preco_total_produto = 11;
//
//     }
//   }
// }
// }
// $magento_orders->$buyerid->id_order[] = '12';
// $magento_orders->$buyerid->mlb_produto[] = '12';
// $magento_orders->$buyerid->sku_produto[] = '12';
// $magento_orders->$buyerid->nome_produto[] = '12';
// $magento_orders->$buyerid->qtd_produto[] = '12';
// $magento_orders->$buyerid->preco_unidade_produto[] = '12';
// $magento_orders->$buyerid->preco_total_produto[] = '12';

 //var_dump($magento_orders);
//
// $pedidosFeitos = retornaPedidosfeitosMGML();
// if(count($mlb) > 1){
// $string = implode(",",$mlb);
//}
// if(!strpos($pedidosFeitos, $string)) echo " Não achou igual"; else echo "Achou";
// var_dump(retornaDadosOrders());

        //$titulo, $nome_funcao, $saida, $mensagem
// $r = new error_handling("Assunto do email", "Função que deu problema", "Debug-> Utilizado serializer", 'Erro');
//
// $r->execute();

// $r = new log("Assunto do email", "Função que deu problema", "Debug-> Utilizado serializer", 'Erro');
// var_dump($r);
//
// $r->log_email = true;
//
// $r->send_log_email();
//
// $r->execute();
// $conteudo_arquivo = file_put_contents("include/files/listaPedidoMLB.json", $listagem);
//
// if(!$conteudo_arquivo) return "Não deu pra escrever a lista de pedidos do mlb";
// else return "Deu pra escrever a lista de pedidos do mlb";
//
// $params = array('access_token' => token(),
//                 'shipment_ids' => "27651542114",
//                 'response_type' => "pdf");
//
// $response = $meli->get('/shipment_labels', $params);
//
//
// $shipment_ids = "27651542114";
// $token = token();
//
// $curl_url =  "https://api.mercadolibre.com/shipment_labels?shipment_ids=$shipment_ids&response_type=pdf&access_token=$token";
// $out = fopen("etiqueta.pdf","wb");
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_FILE, $out);
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_setopt($ch, CURLOPT_URL, $curl_url);
//     curl_exec($ch);
//     curl_close($ch);


//$body = array('source' => "imagens/ep-51-40096_1.jpg");
//$response = $meli->get('/pictures/630052-MLB27727936061_072018' ,$params);
  //
  // $body = array('source' => "imagens/ep-51-40742.jpg");
  // $response = $meli->post('/pictures', $body, $params);

  // $body = array('id' => "630052-MLB27727936061_072018");
  // $response = $meli->post('/items/MLB1039710106/pictures', $body, $params);

   //  echo "<pre>";
    //DEBUG

// $idimg = "811281-MLB27695604871_072018";

// include "include/all_include.php";
// $media_de_cada_passe = 0;
// $i=1;
// do {
//
//   $tempo = time();
//
//
//   echo "QTD de vezes: ".$i;
//
//   echo "";
//
//
//    echo "<br>";
//    echo "TEMPO: ". (time() - $tempo);
//    echo "<br><br><br></h2>";
//  $tempo_ate_agora = (time() - $tempo);
//  $media_de_cada_passe = ($media_de_cada_passe + $tempo_ate_agora)/2;
//
//
//  echo "<br>Media de cada lance: ".$media_de_cada_passe;
//  $i++;
// }
// while (($tempo_ate_agora + $media_de_cada_passe) < 0);

// $appId = "4946951783545211";
// $secretKey = "2tCb5gts3uK8Llf9DQoiSVXnxTKyGuEk";
// $accesstoken = "APP_USR-4946951783545211-062613-e512bd2717f82eb16eb143eb18085bfe-327485416";
// $userid = '327485416';
// USUARIO DE TESTES
//  ["id"]=> int(327485416)
//  ["nickname"]=> string(8) "TT784263"
//  ["password"]=> string(10) "qatest7896"
//  ["site_status"]=> string(6) "active"
//  ["email"]=> string(31) "test_user_97680688@testuser.com" }
// $acessToken = APP_USR-4946951783545211-062613-e512bd2717f82eb16eb143eb18085bfe-327485416

//USUARIO DE TESTES 2
// ["id"]=> int(327509935)
//     ["nickname"]=> string(12) "TEST4CXNCJNZ"
//     ["password"]=> string(10) "qatest8331"
//     ["site_status"]=> string(6) "active"
//     ["email"]=> string(30) "test_user_2645635@testuser.com"
