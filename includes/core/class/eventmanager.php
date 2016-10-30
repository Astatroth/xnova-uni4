<?php

namespace Xcms;

class eventManager
{
	protected static $instance;
	protected $handlers = array();

	protected function __construct(){}

	/**
	 * @static
	 * @return eventManager
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}

		return self::$instance;
	}

	public function addEventHandler ($eventType, $callback, $sort = 100)
	{
		$arEvent = array
		(
			"MESSAGE_ID" 	=> $eventType,
			"CALLBACK" 		=> $callback,
			"SORT" 			=> $sort,
		);

		$eventType = strtoupper($eventType);

		if (!isset($this->handlers[$eventType]))
			$this->handlers[$eventType] = array();

		$this->handlers[$eventType][] = $arEvent;

		core::addLogEvent(__CLASS__, 'Registrer Event: '.$eventType.' Callback: '.implode('::', $callback).'');

		return (count($this->handlers[$eventType]) - 1);
	}

	public function removeEventHandler ($eventType, $eventHandlerKey)
	{
		$eventType = strtoupper($eventType);

		if (is_array($this->handlers[$eventType]))
		{
			if (isset($this->handlers[$eventType][$eventHandlerKey]))
			{
				unset($this->handlers[$eventType][$eventHandlerKey]);
				return true;
			}
		}

		return false;
	}

	public function clearEventHandlers ($eventType)
	{
		$eventType = strtoupper($eventType);

		if (isset($this->handlers[$eventType]))
		{
			unset($this->handlers[$eventType]);
			return true;
		}

		return false;
	}

	public function findEventHandlers ($eventType)
	{
		$eventType = strtoupper($eventType);

		if (!isset($this->handlers[$eventType]) || !is_array($this->handlers[$eventType]))
			return array();

		$handlers = $this->handlers[$eventType];
		if (!is_array($handlers))
			return array();

		uasort($handlers, function($a, $b)
		{
			return ($a['SORT'] <= $b['SORT'] ? 1 : -1);
		});

		return $handlers;
	}

	public function execute ($event, $params = array())
	{
		if (!is_array($params))
			$params = array();

		core::addLogEvent(__CLASS__, 'Call Event: '.$event['MESSAGE_ID'].'');

		if (is_callable($event["CALLBACK"]))
			return call_user_func_array($event["CALLBACK"], $params);
		else
			return true;
	}
}

?>