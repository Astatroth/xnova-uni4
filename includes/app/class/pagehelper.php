<?php

namespace Xnova;

use Xcms\core;
use Xcms\Page AS page;

/**
 * @method setAtribute
 * @method getAtribute
 * @method setTemplate
 * @method setTemplateName
 * @method set
 * @method globals
 */
class pageHelper
{
	/**
	 * @var page
	 */
	public $page;
	public $name = '';
	public $mode = '';

	private $showTopPanel = true;
	private $showLeftMenu = true;
	private $pageTitle = '';
	private $pageContent = '';
	private $pageMessage = '';

	public function __construct()
	{
		$this->page = new page();
	}

	public function __call($name, $arguments)
	{
		if (is_callable(array($this->page, $name)))
		{
			call_user_func_array(array($this->page, $name), $arguments);
		}
	}

	public function showTopPanel ($view = true)
	{
		$this->showTopPanel = $view;
	}

	public function showLeftPanel ($view = true)
	{
		$this->showLeftMenu = $view;
	}

	public function setTitle ($title = '')
	{
		$this->pageTitle = $title;
	}

	public function setContent ($content = '')
	{
		$this->pageContent = $content;
	}

	public function setMessage ($message = '')
	{
		$this->pageMessage = $message;
	}

	public function display()
	{
		global $user;

		$this->page->setAtribute('title', strip_tags($this->pageTitle));

		$this->page->globals('pagePropMode', $this->mode);

		if (!$user->isAuthorized() || isset($_GET['ajax']))
			$this->showLeftMenu = false;

		$params = array();
		$params['topPanel'] = $this->showTopPanel;
		$params['leftMenu'] = $this->showLeftMenu;
		$params['message'] 	= $this->pageMessage;

		$params['timezone'] = (isset($user->data['timezone']) ? $user->data['timezone'] : 0);

		switch (core::getConfig('ajaxNavigation', 0))
		{
			case 0:
				$params['ajaxNavigation'] = 0;
				break;
			case 1:
				$params['ajaxNavigation'] = user::get()->getUserOption('ajax_navigation');
				break;
			default:
				$params['ajaxNavigation'] = 1;
		}

		if (isset($user->data['id']))
		{
			$params['userId'] 			= $user->data['id'];
			$params['deleteUserTimer'] 	= $user->data['deltime'];
			$params['vocationTimer'] 	= $user->data['urlaubs_modus_time'];
			$params['authLevel']		= $user->data['authlevel'];
			$params['newMessages']		= $user->data['new_message'];
			$params['allyMessages']		= ($user->data['ally_id'] != 0) ? $user->data['mnl_alliance'] : 0;
		}

		$this->page->page = $this->pageContent;
		$this->page->display($params);
	}

	protected function message ($text, $title = 'Ошибка', $dest = "", $time = 3, $left = true)
	{
		$this->page->setTemplate('message');
		$this->page->set('text', $text);
		$this->page->set('title', $title);
		$this->page->set('destination', $dest);
		$this->page->set('time', $time);

		$this->setTitle(($title ? strip_tags($title) : 'Сообщение'));
		$this->showTopPanel(false);
		$this->showLeftPanel($left);

		$this->display();
	}
}