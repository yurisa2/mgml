<?php
class event_base
{

  public $nome_funcao = '';
  public $saida = '';
  public $mensagem = '';
  public $tipo = '';
  /**
  * Construtor. Set properties in Magento_order
  */
  public function __construct($nome_funcao, $saida, $mensagem, $tipo)
  {
    $this->nome_funcao = $nome_funcao;
    $this->saida = $saida;
    $this->mensagem = $mensagem;
    $this->tipo = $tipo;
  }
}






?>
