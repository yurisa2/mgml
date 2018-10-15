<?php
ini_set("error_reporting",E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'include/all_include.php';
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

if ($MLB != 0)
{
  $ultimo_MLB = ultimo_MLB();
  echo "<h2>ANTERIOR MLB ATUAL MLB: $MLB<BR>ATUAL MLB: $ultimo_MLB  <BR>";
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
    echo "<h2>atualiza: $atualiza<br>";
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
  // 1 Lista os pedidos existentes no Mercado Livre
  $listapedido = listaPedidoMLB();
  // Caso exista pedido sendo retornado entra no if
  if($listapedido == true)
  {
    echo "<h2>Próximo Pedido: </h2>";
    $mlb = proximoPedidoMLB();
    var_dump($mlb);
    // recupera atraves do .json os pedidos que já foram cadastrados no magento
    $pedidosFeitos = retornaPedidosfeitosMGML();
    // codifica o id do pedido e verifica se é um array
    $string = json_encode($mlb);
    //se for array da um implode para ser usado abaixo
    if(gettype($mlb) == 'array') $string = implode(",",$mlb);
    // verifica se o pedido $string existe no arquivo json
    //se não existir ele entra no if para cadastrar o mesmo
    if(!strpos($pedidosFeitos, $string))
    {
      echo "<h2>Dados do pedido a ser cadastrado</h2>";
      echo "<br>";
      // Retorna um objeto com os dados do pedido a ser inserido no magento
      $Magento_order = retornaObjMl($mlb);
      if($Magento_order == false) return "Sem Novos Pedidos";
      // pega o nome do comprador
      $nome = $Magento_order->nome_comprador;
      // pega o id d entrega
      $id_shipping = $Magento_order->id_shipping;
      // instancia a classe responsavel pela inserção dos pedidos no magento
      $teste = new Magento_order($Magento_order);

      echo "<h2>1 - Criação do customer</h2>";
      // cria cadastro do comprador no magento
      // se ja for cadastrado apenas recupera o id do comprador
      // cria tbm o cadastro do endereço do comprador no magento
      // se for cadastrado recupera as informações
      $id_customer = $teste->magento1_customerCustomerCreate();
      var_dump($id_customer);
      if($id_customer == 0) return escrevePedidoMLB($mlb);
      //
      echo "<br/><h2>2 - Criação do endereço do customer</h2>";
      // Apenas cria um array com os dados do comprador
      $customer_address = $teste->magento2_customerAddressCreate($id_customer);
      var_dump($customer_address);
      if($customer_address == 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>3 - Criação do carrinho de compras</h2>";
      // cria o carrinho de compras, retorna o id do carrinho
      $id_carrinho = $teste->magento3_shoppingCartCreate();
      var_dump($id_carrinho);
      if($id_carrinho == 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>4 - Adicionando os podutos no carrinho</h2>";
      // adiciona os produtos no carrinho
      $add_produto = $teste->magento4_shoppingCartProductAdd($id_carrinho);
      if($add_produto == 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>5 - Lista do podutos no carrinho</h2>";
      // lista os produtos no carrinho
      $produtos_carrinho = $teste->magento5_shoppingCartProductList($id_carrinho);
      var_dump($produtos_carrinho);
      if($produtos_carrinho === 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>6 - Inicializando o customer (shoppingCartCustomerSet)</h2>";
      //seta o comprador com o carrinho
      $customerSet = $teste->magento6_shoppingCartCustomerSet($id_carrinho,$id_customer);
      var_dump($customerSet);
      if($customerSet === 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>7 - Iniciando o endereço do customer no carrinho</h2>";
      //seta o endereço do comprador com o carrinho
      $customerAddressSet = $teste->magento7_shoppingCartCustomerAddresses($id_carrinho);
      var_dump($customerAddressSet);
      if($customerAddressSet === 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>8 - Setando o método de entrega</h2>";
      //seta o meio de pagamento com o carrinho
      $customerEntregaSet = $teste->magento8_shoppingCartShippingMethod($id_carrinho);
      var_dump($customerEntregaSet);
      if($customerEntregaSet === 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>9 - Setando o método de pagamento</h2>";
      //seta o meio de pagamento com o carrinho
      $customerPagamentoSet = $teste->magento9_shoppingCartPaymentMethod($id_carrinho);
      var_dump($customerPagamentoSet);
      if($customerPagamentoSet === 0) return escrevePedidoMLB($mlb);

      echo "<br/><h2>7 - Finalização da compra</h2>";
      // Finaliza a compra
      $order = $teste->magento10_shoppingCartOrder($id_carrinho);
      var_dump($order);
      if($order == 0) return escrevePedidoMLB($mlb);

      // se der certo ele entra aqui e cria criaEtiqueta
      // a classe log faz o serviço
      // o nome do arquivo é um compilado de
      //  id do mercado livre-nome do comprador-id do pedido no magento
      if($order != 0)
      {
        var_dump(escrevePedidoMGML($mlb));

        $nome_arquivo = criaEtiqueta($id_shipping, $mlb, $nome, $order);

        $error_handling = new log("Novo Pedido MAGENTO", "Numero do Pedido MGT: $order", "Comprador: $nome", "nova compra");
        $error_handling->log_email = true;
        $error_handling->mensagem_email = "Nova compra que entrou no magento";
        $error_handling->log_etiqueta = $nome_arquivo;
        $error_handling->log_email = true;
        $error_handling->dir_file = "log/log.json";
        $error_handling->log_files = true;
        $error_handling->send_log_email();
        $error_handling->execute();
      }
    }//caso o strpos retorne a posição - encontre o pedido ja cadastrado retorna aqui
    else echo "<h3>Pedido já existente no MAGENTO</h3><br><br>";
    // escreve o pedido mlb no json ultimomlb para fazer o ciclo de checagem
    var_dump(escrevePedidoMLB($mlb));

    echo "TEMPO Final: ". (time() - $time_inicial);
  }// caso não haja novos pedidos (retorno false na função listaPedidoMLB) entra e exibe o else aqui
  else
  {
    echo "<h3>Nenhum Pedido novo</h3><br><br>";
    echo "TEMPO Final: ". (time() - $time_inicial);
  }
}
  global $tempo_pergunta;
  $tempopergunta = 'include/files/tempo_pergunta.json';
  if(!file_exists($tempopergunta)) file_put_contents($tempopergunta, "");
  $ultimo_emailpergunta = file_get_contents($tempopergunta);
  $perg = new perguntas_respostas;
  $id = $perg->retorna_idPerguntas();
  if($id != false){
    if(($ultimo_emailpergunta + $tempo_pergunta) >= time())
    {

      $error_handling = new log("Nova Pergunta Mercado Livre", "Há nova(s) pergunta(s) no mercado livre ", "Link Abaixo: https://easypath.com.br/conectores/mgml/perguntas_respostas.php", "nova pergunta");
      $error_handling->log_email = true;
      $error_handling->mensagem_email = "Nova Pergunta Mercado Livre";
      $error_handling->log_email = true;
      $error_handling->dir_file = "log/log.json";
      $error_handling->log_files = true;
      $error_handling->email_pergunta = true;
      $error_handling->send_log_email();
      $error_handling->execute();
      file_put_contents($tempopergunta, time());
    }
  }else echo "<b>Sem novas perguntas<b>";

  $cont_script = file_get_contents('include/files/cont_script.json');

  if($cont_script >= 1440)
  {
    $MLB = proximo_MLBimg();
    $ultimo_MLB = ultimo_MLBimg();
    retorna_SKU($MLB);
    atualizaImg($SKU, $MLB);
    file_put_contents('include/files/cont_script.json',"");
    escreve_MLBimg($MLB);
  }

  $cont_script++;
  file_put_contents('include/files/cont_script.json',$cont_script);
