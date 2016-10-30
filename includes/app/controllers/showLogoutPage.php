<?php

namespace Xnova\controllers;

use Xnova\pageHelper;

class showLogoutPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		global $session;

		$session->ClearSession();

		$this->message('Выход', 'Сессия закрыта', "/", 3);
	}
}

?>