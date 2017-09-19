<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair ACP controller base class.
 */
abstract class acp_base_controller implements acp_base_interface
{
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\request\request_interface
	 */
	protected $request;

	/**
	 * @var \phpbb\template\template
	 */
	protected $template;

	/**
	 * The URL for the current page.
	 *
	 * @var string
	 */
	protected $u_action;

	/**
	 * @param ContainerInterface               $container
	 * @param \phpbb\language\language         $language
	 * @param \phpbb\request\request_interface $request
	 * @param \phpbb\template\template         $template
	 */
	public function __construct(ContainerInterface $container, language $language, request_interface $request, template $template)
	{
		$this->container = $container;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
	}

	public function set_page_url($page_url)
	{
		$this->u_action = $page_url;
	}
}
