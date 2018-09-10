<?php

class Magento_order{
  /**
  * Construtor. Set properties in Magento_order
  * @param object $dadosVenda;
  */

  public function __construct($dadosVenda)
  {
    global $magento_soap_user;
    global $magento_soap_password;
    global $store_id;
    global $DEBUG;


    $this->data = new stdClass();
    $this->data->id_order = $dadosVenda->order_id;
    $this->data->mlb_produto = $dadosVenda->mlb_produto;
    $this->data->sku_produto = $dadosVenda->sku_produto;
    $this->data->nome_produto = $dadosVenda->nome_produto;
    $this->data->qtd_produto = $dadosVenda->qtd_produto;
    $this->data->preco_unidade_produto =$dadosVenda->preco_unidade_produto;
    $this->data->preco_total_produto = $dadosVenda->preco_total_produto;

    //--------------PAGAMENTO---------
    $this->data->id_meio_pagamento = $dadosVenda->id_meio_pagamento;
    $this->data->tipo_pagamento = $dadosVenda->tipo_pagamento;
    $this->data->custo_envio = $dadosVenda->custo_envio;
    $this->data->total_pagar = $dadosVenda->total_pagar;
    $this->data->status_pagamento = $dadosVenda->status_pagamento;

    //-----------ENDEREÇO---------
    $this->data->rua = $dadosVenda->rua;
    $this->data->numero = $dadosVenda->numero;
    $this->data->bairro = $dadosVenda->bairro;
    $this->data->cep = $dadosVenda->cep;
    $this->data->cidade = $dadosVenda->cidade;
    $this->data->estado = $dadosVenda->estado;
    $this->data->pais = $dadosVenda->pais;

    // ---------USUARIO---------
    $this->data->id_comprador = $dadosVenda->id_comprador;
    $this->data->apelido_comprador = $dadosVenda->apelido_comprador;
    $this->data->email_comprador = $dadosVenda->email_comprador;
    $this->data->cod_area_comprador = $dadosVenda->cod_area_comprador;
    $this->data->telefone_comprador = $dadosVenda->telefone_comprador;
    $this->data->nome_comprador = $dadosVenda->nome_comprador;
    $this->data->sobrenome_comprador = $dadosVenda->sobrenome_comprador;
    $this->data->tipo_documento_comprador = $dadosVenda->tipo_documento_comprador;
    $this->data->numero_documento_comprador = $dadosVenda->numero_documento_comprador;
  }

  public function magento1_customerCustomerCreate(){
    global $DEBUG;
    $obj_magento = magento_obj();
    $session = magento_session();

    $customer = array(
      'firstname' => $this->data->nome_comprador,
      'lastname' => $this->data->sobrenome_comprador,
      'email' => $this->data->email_comprador,
      'telephone' => $this->data->cod_area_comprador.$this->data->telefone_comprador,
      'taxvat' => $this->data->numero_documento_comprador,
      'group_id' => "1",
      'store_id' => "21",
      'website_id' => "2"
    );

    $complexFilter = array(
      'complex_filter' => array(
        array(
          'key' => 'email',
          'value' => array('key' => 'in', 'value' => $customer['email'])
        )
      )
    );

    $return = $obj_magento->customerCustomerList($session, $complexFilter);
var_dump($return);
    //VERIFICAÇÃO SE EXISTE CLIENTE CADASTRADO COM O EMAIL NO MGNT
    //CASO NÃO EXISTA É CADASTRADO E É PEGO O ID DO CLIENTE
    if(!$return)
    {
      // function magento_customerCustomerCreate()
      $id_customer = $obj_magento->customerCustomerCreate($session, $customer);
      if($id_customer) echo "Customer Cadastrado com sucesso->ID: ".$id_customer;

      if($DEBUG == TRUE)
      {
        echo "<br/><h1>id Customer Novo</h1>";
        var_dump($id_customer);
      }

      $customer_address = array(
        'firstname' => $this->data->nome_comprador,
        'lastname' => $this->data->sobrenome_comprador,
        'street' => array($this->data->rua.", ".$this->data->numero." - ".$this->data->bairro,'' ),
        'city' => $this->data->cidade,
        'country_id' => $this->data->pais,
        'region' => $this->data->estado,
        'postcode' => $this->data->cep,
        'telephone' => $this->data->cod_area_comprador.$this->data->telefone_comprador,
        'is_default_billing' => TRUE,
        'is_default_shipping' => TRUE);

        if($DEBUG == TRUE) var_dump($customer_address);

        $return = $obj_magento->customerAddressCreate($session, $id_customer, $customer_address);
        //lê o json que contem o time() do ultimo email enviado
        if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
        $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

        //Se na requisição para atualizar o produto houver problema (retorno dif de 200)
        // ele entra no bloco de código
        if($return["httpCode"] != 200)
        {
          $nome_funcao = "magento1_customerCustomerCreate";
          $saida = serialize($return);
          $titulo = "Erro no Script Integração Mercado Livre Magento";
          $tipo = "Erro";
          //estancia a classe com os parametros
          $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
          //Se o horario do json + 1 hora (3600 s) for menor ou igual ao horario
          //atual significa que ja passou uma hora e pode mandar novamente email
          if ($hora_email_enviado + 3600 <= time())
          {
            //estancia a função para criar a mensagem de corpo
            $error_handling->send_error_email();
            //estancia a função para executar as funções email()-db()-files() previamente
            //por padrão, as propriedades error_db e error_files estão true
            $error_handling->execute();
            //atualiza o json para a hora em que é mandado o email
            file_put_contents("include/files/ultimo_emailenviado.json", json_encode(time()));
            return "0";
          }
          else
          {
            //Caso não tenha dado uma hora do ultimo email enviado, é gravado
            //o erro no json de log  error_files/error_log.json
            //executa a função para criar a mensagem de erro
            $error_handling->send_errorlog_email();
            //executa a função para atualizar o json com o novo erro
            $error_handling->files();
            return "0";
          }
        }
        else echo "Criado customer Address: ";
        return $return;
        if($DEBUG == TRUE) echo "<br/><h1>AddressesCreate ".$return."</h1>";
      }
      else
      {
        $id_customer = $return[0]->customer_id;
        echo "Id customer::: ";
        return $id_customer;
        if($DEBUG == TRUE) echo "<h1>Customer</h1>";
        if($DEBUG == TRUE) var_dump($id_customer);
      }
    }

    public function magento2_customerAddressCreate($id_customer)
    {
      global $DEBUG;
      $obj_magento = magento_obj();
      $session = magento_session();

      $obj_mag = $obj_magento->customerAddressList($session, $id_customer);
      if($DEBUG == TRUE) {echo "<h1>addressesList</h1>";var_dump($obj_mag);}

      $obj_mag_email = $obj_magento->customerCustomerInfo($session, $id_customer);
      $obj_mag = $obj_mag['0'];

      if($DEBUG == TRUE)
      {
        echo "<h1>CustomerInfo</h1>";
        var_dump($obj_mag);
      }

      $name = $obj_mag->firstname." ".$obj_mag->lastname;
      $email = $obj_mag_email->email;
      $document = preg_replace('/\D/', '',$obj_mag_email->taxvat);
      $city = $obj_mag->city;
      $region = $obj_mag->region;
      $postcode = preg_replace('/\D/', '',$obj_mag->postcode);
      $street = $obj_mag->street;
      $phone = preg_replace('/\D/', '',$obj_mag->telephone);

      $return = array(
        'name' => $name,
        'email' => $email,
        'document' => $document,
        'city' => $city,
        'region' => $region,
        'postcode' => $postcode,
        'street' => $street,
        'phone' => $phone,
      );

      if($DEBUG == true){ echo "<h1>Array Customer</h1>";var_dump($return);}
      return $return;
    }

    public function magento3_shoppingCartCreate()
    {
      global $DEBUG;
      global $store_id;
      $obj_magento = magento_obj();
      $session = magento_session();

      $cart_id = $obj_magento->shoppingCartCreate($session, $store_id);
      //lê o json que contem o time() do ultimo email enviado
      if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
      $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

      if($cart_id) echo "<br/>ID do Carrinho de Compras: ".$cart_id;
      else{
        $nome_funcao = "magento3_shoppingCartCreate";
        $saida = serialize($cart_id);
        $titulo = "Erro no Script Integração Mercado Livre Magento";
        $tipo = "Erro";
        //estancia a classe com os parametros
        $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
        //Se o horario do json + 1 hora (3600 s) for menor ou igual ao horario
        //atual significa que ja passou uma hora e pode mandar novamente email
        if ($hora_email_enviado + 3600 <= time())
        {
          //estancia a função para criar a mensagem de corpo
          $error_handling->send_error_email();
          //estancia a função para executar as funções email()-db()-files() previamente
          //por padrão, as propriedades error_db e error_files estão true
          $error_handling->execute();
          //atualiza o json para a hora em que é mandado o email
          file_put_contents("include/files/ultimo_emailenviado.json", json_encode(time()));
          return "0";
        }
        else
        {
          //Caso não tenha dado uma hora do ultimo email enviado, é gravado
          //o erro no json de log  error_files/error_log.json
          //executa a função para criar a mensagem de erro
          $error_handling->send_errorlog_email();
          //executa a função para atualizar o json com o novo erro
          $error_handling->files();
          return "0";
        }
      }
      return $cart_id;
      if($DEBUG == TRUE) {echo "<h1>shoppingCartCreate</h1>";var_dump($cart_id);}
    }

    public function magento4_shoppingCartProductAdd($cart_id)
    {
      global $DEBUG;
      global $store_id;
      $obj_magento = magento_obj();
      $session = magento_session();
echo "<h1>aqui</h1>";var_dump($this->data->sku_produto);
      foreach ($this->data->sku_produto as $key => $value)
      {

        $shoppingCartProductEntity[$key] = array(
          'sku' => $this->data->sku_produto[$key],
          'qty' => $this->data->qtd_produto[$key]);
      }

        $result_prod_add = $obj_magento->shoppingCartProductAdd($session, $cart_id, $shoppingCartProductEntity, $store_id);
        //lê o json que contem o time() do ultimo email enviado
        if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
        $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

        if ($result_prod_add === true)
        {
          echo "<br/>Itens adicionados no Carrinho: ";
          var_dump($shoppingCartProductEntity);
        }
        else
        {
            echo "<br/>Produtos não puderam ser adicionados";var_dump($result_prod_add);
          $nome_funcao = "magento4_shoppingCartProductAdd";
          $saida = serialize($result_prod_add);
          $titulo = "Erro no Script Integração Mercado Livre Magento";
          $tipo = "Erro";
          //estancia a classe com os parametros
          $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
          //Se o horario do json + 1 hora (3600 s) for menor ou igual ao horario
          //atual significa que ja passou uma hora e pode mandar novamente email
          if ($hora_email_enviado + 3600 <= time())
          {
            //estancia a função para criar a mensagem de corpo
            $error_handling->send_error_email();
            //estancia a função para executar as funções email()-db()-files() previamente
            //por padrão, as propriedades error_db e error_files estão true
            $error_handling->execute();
            //atualiza o json para a hora em que é mandado o email
            file_put_contents("include/files/ultimo_emailenviado.json", json_encode(time()));
            return "0";
          }
          else
          {
            //Caso não tenha dado uma hora do ultimo email enviado, é gravado
            //o erro no json de log  error_files/error_log.json
            //executa a função para criar a mensagem de erro
            $error_handling->send_errorlog_email();
            //executa a função para atualizar o json com o novo erro
            $error_handling->files();
            return "0";
          }
        }
      }

      public function magento5_shoppingCartProductList($cart_id)
      {
        global $DEBUG;
        global $store_id;
        $obj_magento = magento_obj();
        $session = magento_session();
        $result = $obj_magento->shoppingCartProductList($session, $cart_id, $store_id);
        //lê o json que contem o time() do ultimo email enviado
        if(!file_exists("include/files/ultimo_emailenviado.json")) return "Arquivo ultimo_emailenviado.json não existente!";
        $hora_email_enviado = json_decode(file_get_contents("include/files/ultimo_emailenviado.json"));

        //Se na requisição para atualizar o produto houver problema (retorno dif de 200)
        // ele entra no bloco de código
        if($result["httpCode"] != 200)
        {
          $nome_funcao = "magento5_shoppingCartProductList";
          $saida = serialize($result);
          $titulo = "Erro no Script Integração Mercado Livre Magento";
          $tipo = "Erro";
          //estancia a classe com os parametros
          $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
          //Se o horario do json + 1 hora (3600 s) for menor ou igual ao horario
          //atual significa que ja passou uma hora e pode mandar novamente email
          if ($hora_email_enviado + 3600 <= time())
          {
            //estancia a função para criar a mensagem de corpo
            $error_handling->send_error_email();
            //estancia a função para executar as funções email()-db()-files() previamente
            //por padrão, as propriedades error_db e error_files estão true
            $error_handling->execute();
            //atualiza o json para a hora em que é mandado o email
            file_put_contents("include/files/ultimo_emailenviado.json", json_encode(time()));
            return "0";
          }
          else
          {
            //Caso não tenha dado uma hora do ultimo email enviado, é gravado
            //o erro no json de log  error_files/error_log.json
            //executa a função para criar a mensagem de erro
            $error_handling->send_errorlog_email();
            //executa a função para atualizar o json com o novo erro
            $error_handling->files();
            return "0";
          }
        }
        if($DEBUG == TRUE)
        {
          echo "<h1>Produtos adicionados no carrinho: </h1>";
          var_dump($result);
        }
        return "Produtos adicionados no carrinho";
      }
      public function magento6_shoppingCartCustomerSet($cart_id, $id_customer)
      {
        global $DEBUG;
        global $store_id;
        $obj_magento = magento_obj();
        $session = magento_session();

        $customer = array(
          'customer_id' => $id_customer,
          'mode' => "customer"
        );

        $return = $obj_magento->shoppingCartCustomerSet($session, $cart_id, $customer, $store_id);
        if ($return == true)
        {
          return "Setado Customer com sucesso: ";

        }
        else
        {
          $nome_funcao = "magento6_shoppingCartCustomerSet";
          $saida = serialize($return);
          $titulo = "Erro no Script Integração Mercado Livre Magento";
          $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
          $error_handling->execute();
          echo "<br/>Não foi possível Setar Customer";
        }
        if($DEBUG == TRUE) echo "<h1>CartCustomerSet: ".$return."</h1>";
      }
      public function magento7_shoppingCartCustomerAddresses($cart_id)
      {
        global $DEBUG;
        global $store_id;
        $obj_magento = magento_obj();
        $session = magento_session();

        $billing = array(
          array(
            'mode' => 'billing',
            'firstname' => $this->data->nome_comprador,
            'lastname' => $this->data->sobrenome_comprador,
            'street' => $this->data->rua.", ".$this->data->numero." - ".$this->data->bairro,
            'city' => $this->data->cidade,
            'region' => $this->data->estado,
            'postcode' => $this->data->cep,
            'country_id' => $this->data->pais,
            'telephone' => $this->data->cod_area_comprador.$this->data->telefone_comprador,
            'is_default_billing' => TRUE,
            'is_default_shipping' => FALSE),
            array(
              'mode' => 'shipping',
              'firstname' => $this->data->nome_comprador,
              'lastname' => $this->data->sobrenome_comprador,
              'street' => $this->data->rua.", ".$this->data->numero."-".$this->data->bairro,
              'city' => $this->data->cidade,
              'region' => $this->data->estado,
              'postcode' => $this->data->cep,
              'country_id' => $this->data->pais,
              'telephone' => $this->data->cod_area_comprador.$this->data->telefone_comprador,
              'is_default_billing' => FALSE,
              'is_default_shipping' => TRUE)
            );

            $return = $obj_magento->shoppingCartCustomerAddresses($session, $cart_id, $billing, $store_id);
            if ($return == true) return "Setado Customer Addresses no carrinho";
            else {
              $nome_funcao = "magento7_shoppingCartCustomerAddresses";
              $saida = serialize($return);
              $titulo = "Erro no Script Integração Mercado Livre Magento";
              $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
              $error_handling->execute();
              echo "nao deu".var_dump($return);//Mandar email do erro
            }

          }
          public function magento8_shoppingCartShippingMethod($cart_id)
          {
            global $DEBUG;
            global $store_id;
            global $shipping_method;
            $obj_magento = magento_obj();
            $session = magento_session();
            $return = $obj_magento->shoppingCartShippingMethod($session, $cart_id, $shipping_method, $store_id);

            if ($return == true) return "Setado Shipping Method para o carrinho".var_dump($return);
            else {
              $nome_funcao = "magento8_shoppingCartShippingMethod";
              $saida = serialize($return);
              $titulo = "Erro no Script Integração Mercado Livre Magento";
              $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
              $error_handling->execute();

              return "Não foi possivel acionar o metodo de entrega".var_dump($return);//Mandar email do erro
            }
            if($DEBUG == TRUE)
            {
              echo "<h1>shoppingCartShippingMethod</h1>";
              var_dump($return);
            }

          }
          public function magento9_shoppingCartPaymentMethod($cart_id)
          {
            global $DEBUG;
            global $store_id;
            $obj_magento = magento_obj();
            $session = magento_session();

            $payment = array(
              'po_number' => null,
              'method' => 'cashondelivery',
              'cc_cid' => null,
              'cc_owner' => null,
              'cc_number' => null,
              'cc_type' => null,
              'cc_exp_year' => null,
              'cc_exp_month' => null
            );

            $return =  $obj_magento->shoppingCartPaymentMethod($session, $cart_id, $payment, $store_id);

            if ($return == true) return "Setado Payment Method para o carrinho<br/>";
            else {
              $nome_funcao = "magento9_shoppingCartPaymentMethod";
              $saida = serialize($return);
              $titulo = "Erro no Script Integração Mercado Livre Magento";
              $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
              $error_handling->execute();
              echo "Problema meio de pagamento";
            }
            if($DEBUG == TRUE)
            {
              echo "<h1>ShoppingCartPaymentMetod</h1>";
              var_dump($return);
            }
          }
          public function magento10_shoppingCartOrder($cart_id)
          {
            global $DEBUG;
            global $store_id;
            $obj_magento = magento_obj();
            $session = magento_session();

            $order_id = $obj_magento->shoppingCartOrder($session, $cart_id, $store_id);
            if($DEBUG == true){
              if(strlen($order_id) < 11) echo "<br/>Order criado - ".$order_id;
              else {
                $nome_funcao = "magento10_shoppingCartOrder";
                $saida = serialize($order_id);
                $titulo = "Erro no Script Integração Mercado Livre Magento";
                $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
                $error_handling->execute();
                echo '<br/>Deu problema no final--> '.$order_id;
              }
            }
            if($DEBUG == TRUE) {echo "<h1>shoppingCartOrder</h1>";var_dump($order_id);}

            //function magento_salesOrderAddComment($order_id, $status, $comment)
            $comment="";
            foreach ($this->data->id_order as $key =>$value)
            {
              $comment .= "Id do Pedido MLB: ".$this->data->id_order[$key]."\t";
            }

            $return = $obj_magento->salesOrderAddComment($session, $order_id, 'pending', $comment, null);
            if($DEBUG == TRUE)
            {
              if($return == true) echo "<br/>Comentário criado<br/>";
              else {
                $nome_funcao = "magento10_shoppingCartOrder";
                $saida = serialize($return);
                $titulo = "Erro no Script Integração Mercado Livre Magento";
                $error_handling = new error_handling($titulo, $nome_funcao, $saida, "erro");
                $error_handling->execute();
                echo "Não foi possivel adicionar comentario<br/>";
              }
            }
            if($DEBUG == TRUE)
            {
              echo "<h1>salesOrderAddComment</h1><br/>";
              var_dump($return);
            }
            if((strlen($order_id) < 11) && ($return == true)){
              return $order_id;
            }
            else return 0;
          }
        }
