<?php

class Magento_order{
  /**
  * Construtor. Set properties in Magento_order
  * @param object $dadosVenda;
  */

  public function Magento_order($dadosVenda)
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
    //VERIFICAÇÃO SE EXISTE CLIENTE CADASTRADO COM O EMAIL NO MGNT
    //CASO NÃO EXISTA É CADASTRADO E É PEGO O ID DO CLIENTE
    if(!$return)
    {
      // function magento_customerCustomerCreate()
      $id_customer = $obj_magento->customerCustomerCreate($session, $customer);
      if($id_customer) echo "Customer Cadastradocom sucesso->ID: ".$id_customer;
      else{
        $error = "magento1_customerCustomerCreate";
        $debug = var_dump($id_customer);
        $corpo = send_error_email($error, $debug);
        $assunto = "Erro no Script Integração Mercado Livre Magento";
        manda_mail($assunto, $corpo);
      }
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
        if($return) echo "Criado customer Address: ";
        else {
          $error = "magento1_customerCustomerCreate";
          $debug = var_dump($return);
          $corpo = send_error_email($error, $debug);
          $assunto = "Erro no Script Integração Mercado Livre Magento";
          manda_mail($assunto, $corpo);
        }
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
      if($cart_id) echo "<br/>ID do Carrinho de Compras: ".$cart_id;
      else{
        $error = "magento3_shoppingCartCreate";
        $debug = var_dump($cart_id);
        $corpo = send_error_email($error, $debug);
        $assunto = "Erro no Script Integração Mercado Livre Magento";
        manda_mail($assunto, $corpo);
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

      foreach ($this->data->sku_produto as $key => $value)
      {
        $shoppingCartProductEntity[$key] = array(
          'sku' => $this->data->sku_produto[$key],
          'qty' => $this->data->qtd_produto[$key]);
        }

        $result_prod_add = $obj_magento->shoppingCartProductAdd($session, $cart_id, $shoppingCartProductEntity, $store_id);

        if ($result_prod_add === true)
        {
          echo "<br/>Itens adicionados no Carrinho: ";
          var_dump($shoppingCartProductEntity);
        }
        else
        {
          $error = "magento4_shoppingCartProductAdd";
          $debug = var_dump($result_prod_add);
          $corpo = send_error_email($error, $debug);
          $assunto = "Erro no Script Integração Mercado Livre Magento";
          manda_mail($assunto, $corpo);
          echo "<br/>Produtos não puderam ser adicionados".var_dump($result_prod_add);
        }
      }

      public function magento5_shoppingCartProductList($cart_id)
      {
        global $DEBUG;
        global $store_id;
        $obj_magento = magento_obj();
        $session = magento_session();
        $result = $obj_magento->shoppingCartProductList($session, $cart_id, $store_id);
        if(!$result){
          $error = "magento5_shoppingCartProductList";
          $debug = var_dump($result);
          $corpo = send_error_email($error, $debug);
          $assunto = "Erro no Script Integração Mercado Livre Magento";
          manda_mail($assunto, $corpo);
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
          $error = "magento6_shoppingCartCustomerSet";
          $debug = var_dump($return);
          $corpo = send_error_email($error, $debug);
          $assunto = "Erro no Script Integração Mercado Livre Magento";
          manda_mail($assunto, $corpo);
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
              $error = "magento7_shoppingCartCustomerAddresses";
              $debug = var_dump($return);
              $corpo = send_error_email($error, $debug);
              $assunto = "Erro no Script Integração Mercado Livre Magento";
              manda_mail($assunto, $corpo);
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
              $error = "magento8_shoppingCartShippingMethod";
              $debug = var_dump($return);
              $corpo = send_error_email($error, $debug);
              $assunto = "Erro no Script Integração Mercado Livre Magento";
              manda_mail($assunto, $corpo);

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
              $error = "magento9_shoppingCartPaymentMethod";
              $debug = var_dump($return);
              $corpo = send_error_email($error, $debug);
              $assunto = "Erro no Script Integração Mercado Livre Magento";
              manda_mail($assunto, $corpo);
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
                $error = "magento10_shoppingCartOrder";
                $debug = var_dump($order_id);
                $corpo = send_error_email($error, $debug);
                $assunto = "Erro no Script Integração Mercado Livre Magento";
                manda_mail($assunto, $corpo);
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
                $error = "magento10_shoppingCartOrder";
                $debug = var_dump($return);
                $corpo = send_error_email($error, $debug);
                $assunto = "Erro no Script Integração Mercado Livre Magento";
                manda_mail($assunto, $corpo);
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
