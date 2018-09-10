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

// SEM AUTOLOAD
require 'include/PHPMailer/src/Exception.php';
require 'include/PHPMailer/src/PHPMailer.php';
require 'include/PHPMailer/src/SMTP.php';

require_once 'include/mail/mail.php';
require_once 'include/mail/corpo_email.php';

 ?>
