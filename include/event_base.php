<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class event_base
{
  public function __construct()
  {
    /**
    * @param $configmail True se email estiver habilitado/false se não estiver habilitado
    *
    */
    global $configmail;

    /**
    * @property $titulo O assunto do email
    *
    */
    $this->titulo = '';

    /**
    * @property $nome_funcao A função que houve problema
    *
    */
    $this->nome_funcao = '';

    /**
    * @property $saida O debug da função
    *
    */
    $this->saida = '';

    /**
    * @property $tipo Qual a origem/significado da mensagem: Erro - log
    *
    */
    $this->tipo = '';

    /**
    * @property $mensagem Conteudo que irá no corpo do email/arquivo de log .json/gravado no banco de dados
    *
    */
    $this->mensagem = '';

    /**
    * @property $mensagemHTML Conteudo HTML que irá no corpo do email
    *
    */
    $this->mensagemHTML = '';

    /**
    * @property $data A data em segundos para a gravação no banco de dados
    *
    */
    $this->data = time();

    /**
    * @property $error_db CLASSE ERROR HANDLING - True para gravar informações no BD /false para não gravar
    *
    */
    $this->error_db = true;

    /**
    * @property $error_files CLASSE ERROR HANDLING - True para gravar informações no arquivo .json /false para não gravar
    *
    */
    $this->error_files = true;

    /**
    * @property $dir_file Diretório do arquivo de log .json pré-definido (modificavél para classe log)
    *
    */
    $this->dir_file = 'error_files/error_log.json';

    /**
    * @property $flag_HTML Comanda o tipo de dados que irá no corpo do email
    *
    */
    $this->flag_HTML = true;

    /**
    * @property $log_etiqueta Diretorio do arquivo de etiqueta .pdf - vazio caso nao exista necessidade de anexar
    *
    */
    $this->log_etiqueta = '';

    /**
    * @property $log_email CLASSE LOG - Comanda o tipo de dados que irá no corpo do email
    *
    */
    $this->log_email = false;

    /**
    * @property $log_db CLASSE LOG - True para gravar informações no BD /false para não gravar
    *
    */
    $this->log_db = false;

    /**
    * @property $log_files CLASSE LOG - True para gravar informações no arquivo .json /false para não gravar
    *
    */
    $this->log_files = false;

    /**
    * @property $mensagem_email classe log - Titulo do email que sera enviado
    *
    */
    $this->mensagem_email = '';

  }

  /**
  * Function responsible to send the email
  *
  * @return string if failure - Message could not be sent. Mailer Error: ErrorInfo or
  * if was send - e-mail enviado com sucesso!
  *
  */
  public function email()
  {
    global $email_destinatario;
    $e_mail = $email_destinatario[1];
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
    foreach ($email_destinatario as $key => $value) {
      $mail->addAddress($value);
    }

    $mail->addReplyTo($from_mail, $from_name);

    if($this->log_etiqueta !== null) $mail->addAttachment($this->log_etiqueta);

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
  /**
  * Function responsible to save data on DB
  *
  */
  public function db()
  {
    $sqlite = "sqlite:include/event.db";

    $pdo = new PDO($sqlite);
    $sql = "INSERT INTO event(nome_funcao, saida_erro, mensagem, titulo, tipo, data) VALUES (?,?,?,?,?,?)";
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $result = $pdo->prepare($sql);
    $result->bindParam(1, $this->nome_funcao);
    $result->bindParam(2, $this->saida);
    $result->bindParam(3, $this->mensagem);
    $result->bindParam(4, $this->titulo);
    $result->bindParam(5, $this->tipo);
    $result->bindParam(6, $this->data);
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
    $mensagem = json_decode(file_get_contents($this->dir_file));
    $mensagem[] = json_decode($this->mensagem); //incluir opção no encode para caracteres especiais como ç´^~
    $resultado = file_put_contents($this->dir_file, json_encode($mensagem));
    //caso exista + de 100 erros no json manda email com todos.
    //OBS: Pode até mandar o arquivo em anexo;
    if (count($mensagem) > 100)
    {
      $this->titulo = "Erros sei lá";
      foreach ($mensagem as $key => $value)
      {
        foreach ($mensagem[$key] as $i => $values) {
          $this->mensagemHTML.= $i.": ".$values."<br>";
        }
      }
      var_dump($this->mensagemHTML);
      $this->email();
      file_put_contents($this->dir_file, "");
    }
    if($resultado == false) echo "Arquivo não criado em error_files";
    else echo "Concluido!!";
  }

  public function execute()
  {
    global $configmail;
var_dump($this->mensagem);
    if(($configmail) || ($this->log_email)) $this->email();
    if(($this->log_db) || ($this->error_db)) $this->db();
    if(($this->log_files) || ($this->error_files)) $this->files();
  }

}

?>
