<?php

namespace Xcms;

class User
{
	public $data 		= array();
	private $options 	= array('security' => 0);

	/**
	 * Получение параметра пользователя
	 * @param $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->__isset($key) ? $this->data[$key] : null;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function __isset($key)
	{
		return isset($this->data[$key]);
	}

	public function addOptions ($array)
	{
		if (is_array($array))
			$this->options += $array;
	}

	/**
	 * Загрузка параметров пользователя из массива
	 * @param array $array массив параметров
	 * @param bool $parse заполнение массива бонусов
	 */
	public function load_from_array ($array, $parse = true)
	{
		$this->data = $array;

		$eventManager = eventManager::getInstance();

		foreach ($eventManager->findEventHandlers('onAfterUserLoad') AS $event)
			$eventManager->execute($event, Array(&$this->data));

		if ($parse)
			$this->ParseUserData();
	}

	public function load_from_id ($user_id, $fields = array(), $parse = true)
	{
		$user = $this->getById($user_id, $fields);

		if ($user !== false)
			$this->data = $user;
		else
			return false;

		$eventManager = eventManager::getInstance();

		foreach ($eventManager->findEventHandlers('onAfterUserLoad') AS $event)
			$eventManager->execute($event, Array(&$this->data));

		if ($parse)
			$this->ParseUserData();

		return true;
	}

	/**
	 * Получение параметров пользователя по id
	 * @param int $user_id id пользователя
	 * @param array $fields список полей выборки
	 * @return array|bool
	 */
	public function getById ($user_id, $fields = array())
	{
		if (intval($user_id) > 0)
		{
			return db::query("SELECT " . (count($fields) ? implode(',', $fields) : '*') . " FROM game_users WHERE id = " . intval($user_id), true);
		}
		else
			return false;
	}

	private function ParseUserData ()
	{
		if (!isset($this->data['id']))
			return false;

		$eventManager = eventManager::getInstance();

		foreach ($eventManager->findEventHandlers('onBeforeUserParse') AS $event)
			$eventManager->execute($event, Array(&$this->data));

		$this->options = $this->unpackOptions($this->data['options_toggle']);

		foreach ($eventManager->findEventHandlers('onAfterUserParse') AS $event)
			$eventManager->execute($event, Array(&$this));

		return true;
	}

	public function unpackOptions ($opt, $isToggle = true)
	{
		$result = array();

		if ($isToggle)
		{
			$o = array_reverse(str_split(decbin($opt)));

			$i = 0;

			foreach ($this->options AS $k => $v)
			{
				$result[$k] = (isset($o[$i]) ? $o[$i] : 0);

				$i++;
			}
		}

		return $result;
	}

	public function packOptions ($opt, $isToggle = true)
	{
		if ($isToggle)
		{
			$r = array();

			foreach ($this->options AS $k => $v)
			{
				if (isset($opt[$k]))
					$v = $opt[$k];

				$r[] = $v;
			}

			return bindec(implode('', array_reverse($r)));
		}
		else
			return 0;
	}

	public function isAuthorized()
	{
		return (count($this->data) > 0);
	}

	public function isAdmin()
	{
		if ($this->isAuthorized())
			return ($this->data['authlevel'] == 3);
		else
			return false;
	}

	public function getId ()
	{
		return (isset($this->data['id']) ? $this->data['id'] : false);
	}

	public function isNameValid($name)
	{
		if (UTF8_SUPPORT)
			return preg_match('/^[\p{L}\p{N}_\-. ]*$/u', $name);
		else
			return preg_match('/^[A-z0-9_\-. ]*$/', $name);
	}

	public function isMailValid($address)
	{
		if (function_exists('filter_var'))
			return filter_var($address, FILTER_VALIDATE_EMAIL) !== FALSE;
		else
			return preg_match('^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$', $address);
	}

	public function sendMessage ($owner, $sender, $time, $type, $from, $message)
	{
		if (!$time)
			$time = time();

		if (!$owner && $this->isAuthorized())
			$owner = $this->data['id'];

		if (!$owner)
			return false;

		if ($sender === false && $this->isAuthorized())
			$sender = $this->data['id'];

		if ($this->isAuthorized() && $owner == $this->data['id'])
			$this->data['new_message']++;

		sql::build()->insert('game_messages')->set(Array
		(
			'message_owner'		=> $owner,
			'message_sender'	=> $sender,
			'message_time'		=> $time,
			'message_type'		=> $type,
			'message_from'		=> addslashes($from),
			'message_text'		=> addslashes($message)
		))
		->execute();

		sql::build()->update('game_users')->set(Array('+new_message' => 1))->where('id', '=', $owner)->execute();

		return true;
	}

	public function getUserOption ($key = false)
	{
		if ($key === false)
			return $this->options;

		return (isset($this->options[$key]) ? $this->options[$key] : 0);
	}

	public function setUserOption ($key, $value)
	{
		$this->options[$key] = $value;
	}

	public function delete ($userId)
	{
		if (!$userId)
			return false;

		$eventManager = eventManager::getInstance();

		foreach ($eventManager->findEventHandlers('onBeforeUserDelete') AS $event)
		{
			if ($eventManager->execute($event, Array($userId)) == false)
				return false;
		}

		db::query("DELETE FROM game_messages WHERE `message_sender` = '" . $userId . "' OR `message_owner` = '" . $userId . "';");
		db::query("DELETE FROM game_users WHERE `id` = '" . $userId . "';");
		db::query("DELETE FROM game_users_info WHERE `id` = '" . $userId . "';");
		db::query("DELETE FROM game_banned WHERE `who` = '" . $userId . "';");
		db::query("DELETE FROM game_log_ip WHERE `id` = '" . $userId . "';");

		foreach ($eventManager->findEventHandlers('onAfterUserDelete') AS $event)
			$eventManager->execute($event, Array($userId));

		return true;
	}

	public function saveData ($fields, $userId = 0)
	{
		sql::build()->update('game_users')->set($fields)->where('id', '=', ($userId > 0 ? $userId : $this->data['id']))->execute();
	}
}

?>