<?php

namespace Xnova\controllers;

use Xcms\db;
use Xnova\User;
use Xnova\pageHelper;

class showInfokreditsPage extends pageHelper
{
	function __construct ()
	{
		parent::__construct();
	}
	
	public function show ()
	{
		$userinf = db::query("SELECT email FROM game_users_info WHERE id = " . user::get()->getId() . ";", true);

		if (!isset($_SESSION['OKAPI']))
			$this->setTemplate('credits');
		else
			$this->setTemplate('credits_ok');

		$this->set('userid', user::get()->getId());
		$this->set('useremail', $userinf['email']);

		if (isset($_POST['OutSum']) && !isset($_SESSION['OKAPI']))
		{
			do
			{
				$id = mt_rand(1000000000000, 9999999999999);
			}
			while (isset(db::query("SELECT id FROM game_users_payments WHERE transaction_id = ".$id, true)['id']));

			$this->set('invid', $id);
		}

		$this->setTitle('Покупка кредитов');
		$this->showTopPanel(false);
		$this->display();
	}
}

?>