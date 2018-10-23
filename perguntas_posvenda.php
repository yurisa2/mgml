<head>
  <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
  <link href="include/style/formcontrol.css" rel="stylesheet" id="bootstrap-css">
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <!-- Include the above in your HEAD tag ---------->
</head>
<body>
  <?php
  ini_set("error_reporting",E_ALL);
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  require 'include/all_include.php';

  $class = new QuestionsAfterSale;

  $id = $class->retornaPerguntasNaoLidas();
  if(!empty($id->results)){
    $order_id = [];
    foreach ($id as $key => $value) {
      $order_id[] = $id[$key]->order_id;
    }
    file_put_contents("$class->list_message",$order_id);
    $order_id = $class->proximaMensagem();
    $dados = retornaPerguntasPorOrder($order_id);
  ?>
  <div class="container contact-form">
    <div class="contact-image">
      <img src="https://image.ibb.co/kUagtU/rocket_contact.png" alt="rocket_contact"/>
    </div>
    <form method="get" action="perguntas_respostas_be.php">
      <h3>Perguntas Pós Venda - Mercado Livre </h3>
      <?php if (isset($_GET['sucesso'])){ echo '<div class="sucesso" style="color:green;">Mensagem respondida com sucesso</div>';}
      if (isset($_GET['problema'])){ echo '<div class="problema" style="color:red;">Mensagem não pôde ser respondida</div>';}?>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input type="text" name="id" class="form-control" hidden="true" readyonly="true" value=""/>
            <h2>Produto</h2>
            <label type="text" name="produto" class="form-control" readyonly="true" value=""><?php echo $dados['product_name']; ?></label><br>
            <h2>Pergunta</h2>
            <?php foreach ($dados['questions'] as $key => $value) {
              echo '<label type="text" name="pergunta" class="form-control" readyonly="true" value="">'.$value.'</label>';
            }?>
          </div>
          <div class="radio" class="col-md-4">
            <h2>Resposta</h2>
            <textarea name="txtarea" value="resposta" cols="75" rows="5"></textarea><br>

          </div>
          <?php if(isset($_GET['erro'])) echo '<div class="erro" style="color:red;">*Favor digitar a resposta </div><br>'; ?>
            <div class="form-group">
              <input type="submit" name="btnSubmit" class="btnContact" value="<?php if(count($id) >1) echo "Responder e ver próxima"; else echo "Responder";?>" />
            </div>
          </div>

        </div>
      </form>
    </div>
  <?php } else{
     echo '<div class="container contact-form">
    <div class="contact-image">
      <img src="https://image.ibb.co/kUagtU/rocket_contact.png" alt="rocket_contact"/>
    </div>
    <form method="get" action="perguntas_respostas_be.php">
      <h3>Perguntas Pós Venda - Mercado Livre </h3>';
      if (isset($_GET['sucesso'])){ echo '<div class="sucesso" style="color:green;">Mensagem respondida com sucesso</div>';}
      if (isset($_GET['problema'])){ echo '<div class="problema" style="color:red;">Mensagem não pôde ser respondida</div>';}
      echo '<div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <h2>Perguntas</h2>
            <label type="text" name="pergunta" class="form-control" readyonly="true" value="">Nenhuma nova pergunta</label>
          </div>
          </div>

        </div>
      </form>
    </div>';}?>
</body>
