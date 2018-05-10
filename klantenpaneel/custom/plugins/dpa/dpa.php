<?php

namespace Dpa;

use Hook;

class Dpa
{
	public $ClassName;

	public $UrlString;

	public function __construct()
	{
		// strip the namespace
		$this->ClassName = implode('', array_slice(explode('\\', __CLASS__), -1));

		// call hooks
		Hook::addFilter('main_menu', array(__NAMESPACE__, $this->ClassName, 'filter_main_menu'));
	}

	function filter_main_menu($main_menu, $parameters)
	{
		// add to main menu
		$main_menu['Dpa'] = array('title'  => __('mainmenu Dpa', __CLASS__),
								   'url'    => __SITE_URL . '/' . __('dpa', 'url', __CLASS__),
								   'active' => array('dpa'));

		return $main_menu;
	}


	/**
	 * Set the url name for the plugin name here
	 */
	function setUrlString()
	{
		$this->UrlString = __('dpa', 'url', __CLASS__);
	}

	/** function determines if the plugin should be activated (TRUE) or not (FALSE)
	 *
	 * @return bool
	 */
	function activatePlugin()
	{
		// here you can determine if the plugin should be activated or not
		// for example: you could retrieve the invoices of the user, and not activate the plugin if the user has outstanding invoices
		if(1==1)
		{
			// activate plugin
			return TRUE;
		}

		// do not activate plugin
		return FALSE;
	}

}

return __NAMESPACE__;
