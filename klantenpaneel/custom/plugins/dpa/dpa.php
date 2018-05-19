<?php

namespace Dpa;

use Hook;

class Dpa
{
	public $ClassName;

	public $UrlString;

	public function __construct()
	{
		$this->ClassName = implode('', array_slice(explode('\\', __CLASS__), -1));

		Hook::addFilter('main_menu', array(__NAMESPACE__, $this->ClassName, 'filter_main_menu'));
	}

	function filter_main_menu($main_menu, $parameters)
	{
		$main_menu['Dpa'] = array('title'  => __('mainmenu Dpa', __CLASS__),
								   'url'    => __SITE_URL . '/' . __('dpa', 'url', __CLASS__),
								   'active' => array('dpa'));

		return $main_menu;
	}

	function setUrlString()
	{
		$this->UrlString = __('dpa', 'url', __CLASS__);
	}

}

return __NAMESPACE__;
