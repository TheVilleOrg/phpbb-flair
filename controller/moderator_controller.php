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

use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair moderator controller.
 */
class moderator_controller implements moderator_interface
{
	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * @var \stevotvr\flair\operator\flair_interface
	 */
	protected $flair_operator;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\request\request
	 */
	protected $request;

	/**
	 * @var \phpbb\template\template
	 */
	protected $template;

	/**
	 * @var \stevotvr\flair\operator\user_interface
	 */
	protected $user_operator;

	/**
	 * The root phpBB path.
	 *
	 * @var string
	 */
	protected $root_path;

	/**
	 * The script file extension.
	 *
	 * @var string
	 */
	protected $php_ext;

	/**
	 * The URL for the current page.
	 *
	 * @var string
	 */
	protected $u_action;

	/**
	 * @var \p_master
	 */
	protected $p_master;

	/**
	 * @param \phpbb\db\driver\driver_interface			$db
	 * @param \stevotvr\flair\operator\flair_interface	$flair_operator
	 * @param \phpbb\language\language					$language
	 * @param \phpbb\request\request					$request
	 * @param \phpbb\template\template					$template
	 * @param \stevotvr\flair\operator\user_interface	$user_operator
	 * @param string									$root_path		The root phpBB path
	 * @param string									$php_ext		The script file extension
	 */
	public function __construct(driver_interface $db, flair_interface $flair_operator, language $language, request $request, template $template, user_interface $user_operator, $root_path, $php_ext)
	{
		$this->db = $db;
		$this->flair_operator = $flair_operator;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user_operator = $user_operator;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	public function set_page_url($page_url)
	{
		$this->u_action = $page_url;
	}

	public function set_master($p_master)
	{
		$this->p_master = $p_master;
	}

	public function find_user()
	{
		$u_find_username = append_sid($this->root_path . 'memberlist.' . $this->php_ext,
			'mode=searchuser&amp;form=mcp&amp;field=username&amp;select_single=true');
		$u_post_action = append_sid($this->root_path . 'mcp.' . $this->php_ext,
			'i=-stevotvr-flair-mcp-main_module&amp;mode=user_flair');
		$this->template->assign_vars(array(
			'U_FIND_USERNAME'	=> $u_find_username,
			'U_POST_ACTION'		=> $u_post_action,
		));
	}

	public function edit_user_flair()
	{
		$this->language->add_lang('common', 'stevotvr/flair');

		$user_id = $this->request->variable('u', 0);
		$username = $this->request->variable('username', '', true);

		$where = ($user_id) ? 'user_id = ' . (int) $user_id : "username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'";
		$sql = 'SELECT user_id, username, user_colour
				FROM ' . USERS_TABLE . '
				WHERE ' . $where;
		$result = $this->db->sql_query($sql);
		$userrow = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$userrow)
		{
			trigger_error('NO_USER');
		}

		$user_id = (int) $userrow['user_id'];

		if (strpos($this->u_action, '&amp;u=' . $user_id) === false)
		{
			$this->p_master->adjust_url('&amp;u=' . $user_id);
			$this->u_action .= '&amp;u=' . $user_id;
		}

		// add_form_key('mcp_flair');

		if ($this->request->is_set_post('add_flair'))
		{
			$this->change_flair($user_id, 'add');
		}
		else if ($this->request->is_set_post('remove_flair'))
		{
			$this->change_flair($user_id, 'remove');
		}
		else if ($this->request->is_set_post('remove_all_flair'))
		{
			$this->change_flair($user_id, 'remove_all');
		}

		$user_flair = $this->user_operator->get_user_flair($user_id);
		$user_flair = (isset($user_flair[$user_id])) ? $user_flair[$user_id] : array();

		$this->assign_tpl_vars($user_id, $userrow['username'], $userrow['user_colour'], $user_flair);
	}

	/**
	 * Assign the template variables for the user_flair page.
	 *
	 * @param int		$user_id		The ID of the user being worked on
	 * @param string	$username		The username of the user being worked on
	 * @param string	$user_colour	The color of the user being worked on
	 * @param array		$user_flair		The flair items assigned to the user being worked on
	 */
	protected function assign_tpl_vars($user_id, $username, $user_colour, $user_flair)
	{
		$available_flair = $this->flair_operator->get_flair(-1, false, false);
		$categories = array(array('name' => $this->language->lang('FLAIR_UNCATEGORIZED')));
		foreach ($available_flair as $entity)
		{
			if ($entity->is_category())
			{
				$categories[$entity->get_id()]['name'] = $entity->get_name();
				continue;
			}

			$categories[$entity->get_parent()]['flair'][] = $entity;
		}

		$this->template->assign_vars(array(
			'USERNAME_FULL'	=> get_username_string('full', $user_id, $username, $user_colour),

			'U_POST_ACTION'	=> $this->u_action,
		));

		foreach ($categories as $cat_id => $category)
		{
			if (!isset($category['flair']))
			{
				continue;
			}

			$this->template->assign_block_vars('cat', array(
				'CAT_NAME'	=> $category['name'],
			));

			foreach ($category['flair'] as $entity)
			{
				$this->template->assign_block_vars('cat.flair', array(
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),

					'ADD_TITLE'	=> $this->language->lang('MCP_FLAIR_ADD_TITLE', $entity->get_name(), $username),
				));
			}

			if (isset($user_flair[$cat_id]))
			{
				$this->template->assign_block_vars('user_cat', array(
					'CAT_ID'	=> $cat_id,
					'CAT_NAME'	=> $category['name'],
				));

				foreach ($user_flair[$cat_id] as $item)
				{
					$entity = $item['flair'];
					$this->template->assign_block_vars('user_cat.flair', array(
						'FLAIR_ID'			=> $entity->get_id(),
						'FLAIR_NAME'		=> $entity->get_name(),
						'FLAIR_COLOR'		=> $entity->get_color(),
						'FLAIR_ICON'		=> $entity->get_icon(),
						'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
						'FLAIR_COUNT'		=> $item['count'],

						'REMOVE_TITLE'		=> $this->language->lang('MCP_FLAIR_REMOVE_TITLE', $entity->get_name(), $username),
						'REMOVE_ALL_TITLE'	=> $this->language->lang('MCP_FLAIR_REMOVE_ALL_TITLE', $entity->get_name(), $username),
					));
				}
			}
		}
	}

	/**
	 * Make a change to the flair assigned to the user being worked on.
	 *
	 * @param int		$user_id	The ID of the user being worked on
	 * @param string	$change		The type of change to make (add|remove|remove_all)
	 */
	protected function change_flair($user_id, $change)
	{
		// if (!check_form_key('mcp_flair', -1))
		// {
		// 	trigger_error('FORM_INVALID');
		// }

		$action = $this->request->variable($change . '_flair', array('' => ''));
		if (is_array($action))
		{
			list($id, ) = each($action);
		}

		if ($id)
		{
			if ($change === 'remove_all')
			{
				$this->user_operator->set_flair_count($user_id, $id, 0);
				return;
			}

			$counts = $this->request->variable($change . '_count', array('' => ''));
			$count = (isset($counts[$id])) ? (int) $counts[$id] : 1;

			$this->user_operator->{$change . '_flair'}($user_id, $id, $count);
		}

		redirect($this->u_action);
	}
}
