<?php

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

use Xcms\core;
use Xcms\db;
use Xnova\User;

session_start();
define('INSIDE', true);

include($_SERVER['DOCUMENT_ROOT'].'/includes/core/class/core.php');
core::init();

header('Content-Type: application/xml; charset=utf-8');

ksort($_GET);

$params = $_GET;
unset($params['sig']);

$s = '';

foreach($params as $k => $v)
	$s .= $k.'='.$v;

$params = $s;

$signature = md5($params.APPSECRET);

if (strcmp($_GET['sig'], $signature) == 0)
{
	$extra = json_decode($_GET['extra_attributes'], true);

	$amount = intval($_GET['amount']);

	$check = db::query("SELECT id FROM game_users_payments WHERE transaction_id = '" . $_GET['transaction_id'] . "' AND user != 0", true);

	if (!isset($check['id']))
	{
		if (!isset($extra['userId']))
		{
			$error = 1001;
			$errorstr = "Payment is invalid and can not be processed";
			$result = "Error amount: {$amount} okid: {$_GET['uid']}";
		}
		else
		{
			$user = db::query("SELECT id FROM game_users WHERE id = " . intval($extra['userId']) . "", true);

			if (!isset($user['id']))
			{
				$error = 1001;
				$errorstr = "Payment is invalid and can not be processed";
				$result = "Not found user: {$_GET['amount']} {$_GET['uid']}";
			}
			else
			{
				if ($amount == 20 || $amount == 60 || $amount == 100 || $amount == 200 || $amount == 500)
					$amount += floor($amount * 0.1);

				if ($amount > 0)
				{
					db::query("UPDATE game_users SET credits = credits + " . $amount . " WHERE id = " . $user['id'] . "");

					user::get()->sendMessage($user['id'], 0, 0, 1, 'Обработка платежей', 'На ваш счет зачислено ' . $amount . ' кредитов');

					db::query("INSERT INTO game_users_payments (user, call_id, method, transaction_id, transaction_time, uid, amount) VALUES (" . $user['id'] . ", '" . $_GET['call_id'] . "', '" . $_GET['method'] . "', '" . $_GET['transaction_id'] . "', '" . $_GET['transaction_time'] . "', '" . $_GET['uid'] . "', " . $amount . ")");

					$result = "Byed ok: {$amount}.";
					$error = 0;
				}
				else
				{
					$error = 1001;
					$errorstr = "Payment is invalid and can not be processed";
					$result = "Error amount: {$amount} okid: {$_GET['uid']}";
				}
			}
		}
	}
	else
	{
		$result = "Byed ok: {$amount}.";
		$error = 0;
	}
}
else
{
	$error = 104;
	$errorstr = "Invalid signature";
	$result ="Invalid signature".$_GET['sig']." ".$signature;
}

if (!$error)
{
	echo '<?xml version="1.0" encoding="UTF-8"?><callbacks_payment_response xmlns="http://api.forticom.com/1.0/">true</callbacks_payment_response>';
}
else
{
	db::query("INSERT INTO game_users_payments (user, call_id, method, transaction_id, transaction_time, uid, amount) VALUES (0, '".$_GET['call_id']."', '".$_GET['method']."', '".$_GET['transaction_id']."', '".$_GET['transaction_time']."', '".$_GET['uid']."', -1)");

	printMsg($error, $errorstr);
}

function printMsg($error, $errorstr)
{
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?><ns2:error_response xmlns:ns2=\"http://api.forticom.com/1.0/\"><error_code>{$error}</error_code><error_msg>{$errorstr}</error_msg></ns2:error_response>";
}

?>