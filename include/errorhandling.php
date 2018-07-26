<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class error_handling extends event_base
{
  public function __construct($titulo, $nome_funcao, $saida, $mensagem, $acao)
  {
      parent::event_base($titulo, $nome_funcao, $saida, $mensagem, $acao);
      
  }

}

?>
