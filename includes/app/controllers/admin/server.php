<?php

/**
 * @var $this \Xnova\pageHelper
 */

use Xnova\User;

if (!defined("INSIDE"))
	die("attemp hacking");

if (user::get()->data['authlevel'] >= 3)
{
	$this->setTemplate('server');
	$this->setTitle('Серверное окружение');
	$this->display();
}
else
	$this->message(_getText('sys_noalloaw'), _getText('sys_noaccess'));

?>