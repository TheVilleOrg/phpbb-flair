<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\mcp;

/**
 * Profile Flair MCP module.
 */
class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	/**
	 * @var p_master
	 */
	protected $p_master;

	/**
	 * @param p_master $p_master
	 */
	public function __construct($p_master)
	{
		$this->p_master = $p_master;
	}

	public function main($id, $mode)
	{
		global $phpbb_container;
		$controller = $phpbb_container->get('stevotvr.flair.controller.moderator');
		$request = $phpbb_container->get('request');

		$this->page_title = 'MCP_FLAIR_TITLE';

		$show_user_flair = $request->variable('u', 0) || $request->variable('username', '', true);
		$this->p_master->set_display($id, 'user_flair', $show_user_flair);

		$controller->set_page_url($this->u_action);
		$controller->set_master($this->p_master);

		switch ($mode)
		{
			case 'user_flair':
				$this->tpl_name = 'mcp_flair_user';
				$controller->edit_user_flair();
			break;
			default:
				$this->tpl_name = 'mcp_flair_front';
				$controller->find_user();
			break;
		}
	}
}
