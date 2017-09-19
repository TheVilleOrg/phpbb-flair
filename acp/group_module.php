<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\acp;

/**
 * Profile Flair group ACP module.
 */
class group_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$controller = $phpbb_container->get('stevotvr.flair.controller.acp.group');
		$request = $phpbb_container->get('request');

		$this->tpl_name = 'group';
		$this->page_title = 'ACP_FLAIR_MANAGE_GROUPS';

		$controller->set_page_url($this->u_action);

		$user_id = $request->variable('subject_id', 0);

		if (!$user_id)
		{
			$controller->select_subject();
			return;
		}

		$controller->edit_subject_flair();
	}
}
