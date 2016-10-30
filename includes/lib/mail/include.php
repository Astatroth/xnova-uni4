<?php

use Xcms\core;

core::registerAutoloadClass(array
(
	'PHPMailer' => ROOT_DIR.LIB_PATH.'mail/class.phpmailer.php',
	'POP3' 		=> ROOT_DIR.LIB_PATH.'mail/class.pop3.php',
	'SMTP' 		=> ROOT_DIR.LIB_PATH.'mail/class.smtp.php',
));
 
?>