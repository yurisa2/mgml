<?php
class event_base
{
  /**
  * Construtor. Set properties in Magento_order
  */
  public function event_base($titulo, $nome_funcao, $saida, $mensagem, $acao)
  {
    $ob = new stdClass;
    $ob->titulo = $titulo;
    $ob->nome_funcao = $nome_funcao;
    $ob->saida = $saida;
    $ob->mensagem = $mensagem;
    $ob->acao = $acao;
  }
  public function email()
  {
    $e_mail = 'luigifracalanza@gmail.com';
    $from_mail = 'mercomagento@sa2.com.br';
    $from_name = 'BOT - Integração Mercado Livre Magento Sa2 - BOT';
    $titulo = $this->titulo;
    $mensagem = $this->mensagem;
    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.sendgrid.net';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'mercomagento';                 // SMTP username
    $mail->Password = '01merco02magento';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    //MUDAR ISSO De volta quando entrar em produção estável


    $mail->CharSet = 'UTF-8';  //Arrumar acentuação

    $mail->setFrom($from_mail, $from_name);
    $mail->addAddress($e_mail);               // Name is optional

    $mail->addReplyTo($from_mail, $from_name);



    /*
    $mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
    $mail->addAddress('ellen@example.com');               // Name is optional
    $mail->addReplyTo('info@example.com', 'Information');
    $mail->addCC('cc@example.com');
    $mail->addBCC('bcc@example.com');

    $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    */
    //escreve_log_mail($assunto,$corpo,$e_mail);
    $mail->addAttachment('etiqueta.pdf');
    //$mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $titulo;
    $mail->Body    = $menssagem;
    $mail->AltBody = strip_tags($mensagem);

     if(!$mail->send())
     {
         echo 'Message could not be sent.';
         echo 'Mailer Error: ' . $mail->ErrorInfo;
     }
     else echo "e-mail enviado com sucesso!<br>";

  }

  public function db()
  {

  }

  public function files()
  {
    $mensagem = $this->mensagem;
    $resultado = file_put_contents("error_files/error_log.json", json_encode($mensagem));

    if($resultado == false) echo "Arquivo não criado em error_files";
    else echo "Concluido!!";
  }
}

?>
