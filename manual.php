<head>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<link href="include/style/formcontrol.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Include the above in your HEAD tag ---------->
</head>
<div class="container contact-form">
            <div class="contact-image">
                <img src="https://image.ibb.co/kUagtU/rocket_contact.png" alt="rocket_contact"/>
            </div>
            <form method="get" action="manual_be.php">
                <h3>Checagem manual - Mercado Livre - Magento</h3>
               <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" name="mlb" class="form-control" placeholder="MLB" value="" />
                        </div>
                        <div class="radio" class="col-md-4">
                          <label><input type="radio" name="optradio" value="Sinc">Atualizar Produto - <small>atualizaProdML($mlb)</small></label><br>
                          <label><input type="radio" name="optradio" value="Resumo">Resumo Gestão - <small>resumoProd($mlb)</small></label><br>
                          <label><input type="radio" name="optradio" value="setloop">Setar o começo do loop - <small>setarInicioLoop($mlb)</small></label><br>
                          <label><input type="radio" name="optradio" value="listMgt">Listar produtos Magento - <small>listaProdMgt()</small></label><br>
                          <label><input type="radio" name="optradio" value="listml">Listar produtos ML - <small>listaProdML()</small></label><br>
                          <label><input type="radio" name="optradio" value="mail">Testar eMail - <small>testmail()</small></label>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="btnSubmit" class="btnContact" value="Acessar" />
                        </div>
                    </div>

                </div>
            </form>
</div>
