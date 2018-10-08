<?php
require_once 'include/config.php';
require_once 'include/funcoes.php';
require_once 'include/orderAdd.php';
require_once 'include/event_base.php';
require_once 'include/error_handling.php';
require_once 'include/log.php';

require_once 'include/ml/php-sdk/Meli/meli.php';
require_once 'include/ml/php-sdk/configApp.php';

require_once 'include/apimagentophp/include/all_include.php';

//COM AUTOLOAD
// require 'include/PHPMailer/vendor/autoload.php';

require_once 'include/class_perguntas_respostas.php';

// SEM AUTOLOAD
require_once 'include/PHPMailer/src/Exception.php';
require_once 'include/PHPMailer/src/PHPMailer.php';
require_once 'include/PHPMailer/src/SMTP.php';

 ?>
