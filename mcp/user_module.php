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
 * Profile Flair user MCP module.
 */
class user_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	private $p_master;

	public function __construct($p_master)
	{
		$this->p_master = $p_master;
	}

	public function main($id, $mode)
	{
		global $phpbb_container;
		$controller = $phpbb_container->get('stevotvr.flair.controller.mcp.user');
		$request = $phpbb_container->get('request');

		$this->tpl_name = 'mcp_user';
		$this->page_title = 'MCP_FLAIR';

		$controller->set_page_url($this->u_action);

		$user_id = $request->variable('u', 0);
		$username = $request->variable('username', '', true);

		if (!$user_id && !$username)
		{
			$controller->find_user();
			$this->p_master->set_display($id, 'user_flair', false);
			return;
		}

		$db = $phpbb_container->get('dbal.conn');
		$where = ($user_id) ? 'user_id = ' . (int) $user_id : "username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";
		$sql = 'SELECT user_id, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE ' . $where;
		$db->sql_query($sql);
		$userrow = $db->sql_fetchrow();
		$db->sql_freeresult();

		if (!$userrow)
		{
			$language = $phpbb_container->get('language');
			trigger_error($language->lang('NO_USER'), E_USER_WARNING);
		}

		$user_id = (int) $userrow['user_id'];

		if (strpos($this->u_action, '&amp;u=' . $user_id) === false)
		{
			$this->p_master->adjust_url('&amp;u=' . $user_id);
			$this->u_action .= '&amp;u=' . $user_id;
		}

		if (!$user_id)
		{
			$controller->find_user();
			return;
		}

		$controller->edit_user_flair($userrow);
	}
}
