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
  $perg = new perguntas_respostas;

  $id = $perg->retorna_idPerguntas();

  if($id != false) {
  $array_perguntas = $perg->retorna_pergunta($id);
  $prox = $perg->proximaIdPergunta();
  $pergunta = $perg->proximaPergunta($prox);
  ?>
  <div class="container contact-form">
    <div class="contact-image">
      <img src="https://image.ibb.co/kUagtU/rocket_contact.png" alt="rocket_contact"/>
    </div>
    <form method="get" action="perguntas_respostas_be.php">
      <h3>Perguntas e Respostas - Mercado Livre (<?php echo count($id); ?>)</h3>
      <?php if (isset($_GET['sucesso'])){ echo '<div class="sucesso" style="color:green;">Mensagem respondida com sucesso</div>';}
      if (isset($_GET['problema'])){ echo '<div class="problema" style="color:red;">Mensagem não pôde ser respondida</div>';}?>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <input type="text" name="id" class="form-control" hidden="true" readyonly="true" value="<?php echo $pergunta->ID; ?>"/>
            <h2>Produto</h2>
            <label type="text" name="produto" class="form-control" readyonly="true" value=""><?php echo $pergunta->PRODUTO; ?></label><br>
            <h2>Pergunta</h2>
            <label type="text" name="pergunta" class="form-control" readyonly="true" value=""><?php echo $pergunta->PERGUNTA; ?></label>
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
      <h3>Perguntas e Respostas - Mercado Livre</h3>';
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
