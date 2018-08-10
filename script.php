<?php
require 'include/all_include.php';
ini_set("error_reporting",E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 1);
// 1 - Ler a lista de produtos no ML (arquivo list.php)
// 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)
// 3 - Descobrir o SKU através do MLB do próximo
// 4 - Rodar a função de atualização com os dois dados
// 5 - Escrever o ultimo no json
//
// $lista_produto = lista_MLB();

$DEBUG = false;

$time_inicial = time();
echo "<pre><br>";
echo "TEMPO: ". (time() - $time_inicial);
echo "<br>";

echo "<h2>1 - Ler a lista de produtos no ML (arquivo list.php)  -- IMPLICITO NO PROXIMO PASSO</h2>";
echo "<br>";

echo "<h2>2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)</h2>";
echo "<br>";
$MLB = proximo_MLB(); // 2 - Localizar ultimo (json) e o próximo da lista (array que vem do list)

if ($MLB != 0){

  $ultimo_MLB = ultimo_MLB();
  echo "<h2>ATUAL MLB: $MLB<BR>ANTERIOR MLB: $ultimo_MLB  <BR>";
  echo "TEMPO:". (time() - $time_inicial);
  echo "<br><br><br></h2>";

  echo "<h2>3 - Descobrir o SKU através do MLB do próximo</h2>";
  $SKU = retorna_SKU($MLB); // 3 - Descobrir o SKU através do MLB do próximo
  echo "<br>";
  echo "<h2>ATUAL SKU: ". $SKU ."<BR>PROXIMO SKU:". retorna_SKU($ultimo_MLB) ."<BR>";
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br></h2>";

  if((isset($MLB) && isset($SKU)) || ($MLB != 0 && $SKU != 0))
  {
    echo "<h2>4 - Rodar a função de atualização com os dois dados</h2>";
    echo "<br>";
    $atualiza = atualizaMLB($SKU,$MLB); // 4 - Rodar a função de atualização com os dois dados
    echo "<h2>atualiza: ".var_dump($atualiza)."<BR>";
  }
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br></h2>";
  // if($atualiza)
  // {
  echo "<h2>5 - Escrever o ultimo MLB atualizado no json</h2>";
  echo "<br>";
  $escreveMLB = escreve_MLB($MLB); // 5 - Escrever o ultimo no json
  echo "<h2>escreveMLB: $escreveMLB <BR>";
  // }
  echo "TEMPO: ". (time() - $time_inicial);
  echo "<br><br><br></h2>";

  // JUNÇÃO ML MG SCRIPT PARA POR O PEDIDO DO ML NO Magento_order
  echo "<h2>JUNÇÃO ML MG SCRIPT PARA POR O PEDIDO DO ML NO Magento</h2><br/>Lista de Pedidos MLB<br/>";
  $listapedido = listaPedidoMLB();

  if($listapedido != 0)
  {
    echo "<h2>Dados do pedido a ser cadastrado</h2>";
    echo "<br>";
    $Magento_order = retornaObjMl();
    var_dump($Magento_order);
    $nome = $Magento_order->nome_comprador;
    $id_shipping = $Magento_order->id_shipping;

    echo "<h2>Próximo Pedido: </h2>";
    $mlb = proximoPedidoMLB();
    var_dump($mlb);

    $teste = new Magento_order($Magento_order);

    $pedidosFeitos = retornaPedidosfeitosMGML();
    $string = implode(",",$mlb);
    if(!strpos($pedidosFeitos, $string)){
      echo "<h2>1 - Criação do customer</h2>";
      $id_customer = $teste->magento1_customerCustomerCreate();
      var_dump($id_customer);

      echo "<br/><h2>2 - Criação do endereço do customer</h2>";
      $customer_address = $teste->magento2_customerAddressCreate($id_customer);
      var_dump($customer_address);

      echo "<br/><h2>3 - Criação do carrinho de compras</h2>";
      $id_carrinho = $teste->magento3_shoppingCartCreate();
      var_dump($id_carrinho);

      echo "<br/><h2>4 - Adicionando os podutos no carrinho</h2>";
      $add_produto = $teste->magento4_shoppingCartProductAdd($id_carrinho);
      var_dump($add_produto);

      echo "<br/><h2>5 - Lista do podutos no carrinho</h2>";
      $produtos_carrinho = $teste->magento5_shoppingCartProductList($id_carrinho);
      var_dump($produtos_carrinho);

      echo "<br/><h2>6 - Inicializando o customer (shoppingCartCustomerSet)</h2>";
      $customerSet = $teste->magento6_shoppingCartCustomerSet($id_carrinho,$id_customer);
      var_dump($customerSet);

      echo "<br/><h2>7 - Iniciando o endereço do customer no carrinho</h2>";
      $customerAddressSet = $teste->magento7_shoppingCartCustomerAddresses($id_carrinho);
      var_dump($customerAddressSet);

      echo "<br/><h2>8 - Setando o método de entrega</h2>";
      $customerEntregaSet = $teste->magento8_shoppingCartShippingMethod($id_carrinho);
      var_dump($customerEntregaSet);

      echo "<br/><h2>9 - Setando o método de pagamento</h2>";
      $customerPagamentoSet = $teste->magento9_shoppingCartPaymentMethod($id_carrinho);
      var_dump($customerPagamentoSet);

      echo "<br/><h2>7 - Finalização da compra</h2>";
      $order = $teste->magento10_shoppingCartOrder($id_carrinho);
      var_dump($order);

      if($order != 0)
      {
        var_dump(escrevePedidoMGML($mlb));

        $nome_arquivo = criaEtiqueta($id_shipping, $mlb, $nome, $order);

        $error_handling = new log("Novo Pedido MAGENTO", "Numero do Pedido MGT: $order", "Comprador: $nome", "nova compra");
        $error_handling->log_email = true;
        $error_handling->mensagem_email = "Nova compra que entrou no magento";
        $error_handling->etiqueta = $nome_arquivo;
        $error_handling->log_email = true;
        $error_handling->dir_files = "log/log.json";
        $error_handling->log_files = true;
        $error_handling->send_log_email();
        $error_handling->execute();
      }
    }
    else echo "Pedido já existente no MAGENTO";

    var_dump(escrevePedidoMLB($mlb));

    echo "TEMPO Final: ". (time() - $time_inicial);
  }
  else {
    echo "Nenhum Pedido novo";
  }
}
