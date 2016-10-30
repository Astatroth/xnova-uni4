<?php

use Xcms\core;
use Xcms\db;
use Xnova\User;

define('INSIDE', true);
header("Content-type: text/html; charset=utf-8");

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();

if ($_REQUEST["InvId"] == '' || !is_numeric($_REQUEST["InvId"]))
	die('InvId nulled');
	
$sign_hash = strtoupper(md5("".$_REQUEST['OutSum'].":".$_REQUEST['InvId'].":".SHOP_SECRET.":Shp_UID=".$_REQUEST['Shp_UID'].""));

if (strtoupper($_REQUEST["SignatureValue"]) === $sign_hash) 
{
	$check = db::query("SELECT id FROM game_users_payments WHERE transaction_id = '".intval($_REQUEST["InvId"])."' AND user != 0", true);

	if (!isset($check['id']))
	{
		$user = db::query("SELECT id FROM game_users WHERE id = ".intval($_REQUEST["Shp_UID"])." LIMIT 1", true);

		if (isset($user['id']))
		{
			$amount = intval($_REQUEST['OutSum']);

			if ($amount > 0)
			{
				//$amount += round($amount * 0.05);

				db::query("UPDATE game_users SET credits = credits + ".$amount." WHERE id = ".$user['id']."");
				db::query("INSERT INTO game_users_payments (user, call_id, method, transaction_id, transaction_time, uid, amount, product_code) VALUES (".$user['id'].", '', '".addslashes($_REQUEST['IncCurrLabel'])."', '".intval($_REQUEST["InvId"])."', '".date("Y-m-d H:i:s", time())."', '0', ".$amount.", '".addslashes(json_encode($_REQUEST))."')");

				user::get()->sendMessage($user['id'], 0, 0, 1, 'Обработка платежей', 'На ваш счет зачислено '.$amount.' кредитов');

				db::query("INSERT INTO game_log_credits (uid, time, credits, type) VALUES (" . $user['id'] . ", " . time() . ", " . $amount . ", 1)");

				echo 'OK'.$_REQUEST["InvId"];

				//socials::smsSend(SMS_LOGIN, 'new payment #'.intval($_REQUEST["InvId"]).': '.$amount.' rub, uid: '.$user['id'].'', $token);
			}
		}
		else
			echo 'userId not found';
	}
	else
		echo 'already paid';
}
else
	echo 'signature verification failed';

?>