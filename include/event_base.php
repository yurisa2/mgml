<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class event_base
{
  /**
  * Construtor. Set properties
  */
  public function __construct()
  {
    global $configmail;

    $this->titulo = '';
    $this->nome_funcao = '';
    $this->saida = '';
    $this->mensagem = '';
    $this->tipo = '';
    $this->mensagemHTML = '';
    $this->flag_HTML = $configmail;
    $this->etiqueta = '';
    $this->email = false;
  }

  /**
    * Function response to send the email
    *
    * @return string if failure - Message could not be sent. Mailer Error: ErrorInfo or
    * if was send - e-mail enviado com sucesso!
    *
  */
  public function email()
  {
    $e_mail = 'luigifracalanza@gmail.com';
    $from_mail = 'mercomagento@sa2.com.br';
    $from_name = 'BOT - Integração Mercado Livre Magento Sa2 - BOT';
    $titulo = $this->titulo;
    $mensagem = $this->mensagemHTML;
    $mail = new PHPMailer;

    //$mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.sendgrid.net';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'mercomagento';                 // SMTP username
    $mail->Password = '01merco02magento';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->CharSet = 'utf-8';  //Arrumar acentuação
    $mail->setFrom($from_mail, $from_name);
    $mail->addAddress($e_mail);               // Name is optional
    $mail->addReplyTo($from_mail, $from_name);

    //if($this->etiqueta !== null) $mail->addAttachment($this->etiqueta);

    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $titulo;
    $mail->Body    = $mensagem;
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
    $sqlite = "sqlite:include/event_base.db";

    $pdo = new PDO($sqlite);
    $sql = "INSERT INTO event(nome_funcao, saida_funcao, mensagem, titulo, tipo) VALUES (?,?,?,?,?)";
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $result = $pdo->prepare($sql);
    $result->bindParam(1, $this->nome_funcao);
    $result->bindParam(2, $this->saida);
    $result->bindParam(3, $this->mensagem);
    $result->bindParam(4, $this->titulo);
    $result->bindParam(5, $this->tipo);
    $result->execute();
    $select = $result->fetchAll(PDO::FETCH_ASSOC);

    var_dump($select);
  }
  /**
    * Function to write an json file
    *
    * @return string if failure - Arquivo não criado em error_files or
    * if was true - Concluido!!
    *
  */
  public function files()
  {
    $mensagem = json_decode(file_get_contents('error_files/error_log.json'));
    $mensagem[] = $this->mensagem;
    $resultado = file_put_contents("error_files/error_log.json", json_encode($mensagem, JSON_UNESCAPED_UNICODE));

    if($resultado == false) echo "Arquivo não criado em error_files";
    else echo "Concluido!!";
  }

  public function execute()
  {
    global $configmail;

    $this->send_error_email();
    if(($configmail) || ($this->email)) $this->email();
    // $this->db();
    $this->files();
  }
}

?>
