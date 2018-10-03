<?php
//MAGENTO
$prefix_m = '';

//ML     CREDENCIAIS fornecidas pelo mercado livre
$app_Id = "";
$secret_Key = "";
$user_id = "";
$site_id = "";

//Ajustes
$ajuste_preco_multiplicacao = "";     // desconto dado aos produtos (0.2 = 20%/0.3 = 30%)
$ajuste_estoque = "";                 // taxa de ajuste de estoque  (0.2 = 20%/0.3=30% de dimunuição do estoque)
$sufixo_prod = "";                    // sufixo para os titulos dos anuncios/produtos
$prefixo_prod = "";                   // prefixo para os titulos dos anuncios/produtos
$marca = "";                          // marca para a rotina de atualização dos anuncios
$ajuste_preco_soma = "";              // adição de um valor X para o preço dos produtos (5.0/6.0)

//ML
$configmail = true;                   // true para habilitar o envio de email
$email_destinatario = array();        // array com um ou mais emails para receber os emails de erro e nova compra

// Ainda há problemas não encontrados para o uso do Sendmail

$SMTP = false;                        // se SMTP é false, sendmail será usado

$Host = '';                           // Specify main and backup SMTP servers
$SMTPAuth = ;                         // Enable SMTP authentication
$Username = '';                       // SMTP username
$Password = '';                       // SMTP password
$SMTPSecure = '';                     // Enable TLS encryption, `ssl` also accepted
?>
