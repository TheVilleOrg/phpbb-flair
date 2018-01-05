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

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\user;
use stevotvr\flair\operator\category_interface;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair user ACP controller.
 */
class acp_user_controller extends acp_base_controller implements acp_user_interface
{
	/**
	 * @var \phpbb\config\config
	 */
	protected $config;

	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * @var \phpbb\user
	 */
	protected $user;

	/**
	 * @var \stevotvr\flair\operator\category_interface
	 */
	protected $cat_operator;

	/**
	 * @var \stevotvr\flair\operator\flair_interface
	 */
	protected $flair_operator;

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
	 * Set up the controller.
	 *
	 * @param \phpbb\config\config                        $config
	 * @param \phpbb\db\driver\driver_interface           $db
	 * @param \phpbb\user                                 $user
	 * @param \stevotvr\flair\operator\category_interface $cat_operator
	 * @param \stevotvr\flair\operator\flair_interface    $flair_operator
	 * @param \stevotvr\flair\operator\user_interface     $user_operator
	 */
	public function setup(config $config, driver_interface $db, user $user, category_interface $cat_operator, flair_interface $flair_operator, user_interface $user_operator)
	{
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
		$this->user_operator = $user_operator;
	}

	/**
	 * Set the phpBB installation path information.
	 *
	 * @param string $root_path The root phpBB path
	 * @param string $php_ext   The script file extension
	 */
	public function set_path_info($root_path, $php_ext)
	{
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	public function find_user()
	{
		$this->language->add_lang('acp/users');

		$u_find_username = append_sid($this->root_path . 'memberlist.' . $this->php_ext,
			'mode=searchuser&amp;form=select_user&amp;field=username&amp;select_single=true');

		$this->template->assign_vars(array(
			'S_SELECT_USER'		=> true,

			'U_ACTION'			=> $this->u_action,
			'U_FIND_USERNAME'	=> $u_find_username,
		));
	}

	public function edit_user_flair()
	{
		$user_id = $this->request->variable('user_id', 0);
		$username = $this->request->variable('username', '', true);

		$where = ($user_id) ? 'user_id = ' . (int) $user_id : "username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'";
		$sql = 'SELECT user_id, username
				FROM ' . USERS_TABLE . '
				WHERE ' . $where;
		$result = $this->db->sql_query($sql);
		$userrow = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$userrow)
		{
			trigger_error($this->language->lang('NO_USER') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$user_id = (int) $userrow['user_id'];

		add_form_key('edit_user_flair');

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

		$user_flair = $this->user_operator->get_flair($user_id);
		$this->assign_tpl_vars($user_id, $userrow['username'], $user_flair);
	}

	/**
	 * Assign the template variables for the page.
	 *
	 * @param int    $user_id    The ID of the user being worked on
	 * @param string $user_name  The name of the user being worked on
	 * @param array  $user_flair The flair items assigned to the user being worked on
	 */
	protected function assign_tpl_vars($user_id, $user_name, array $user_flair)
	{
		$this->template->assign_vars(array(
			'FLAIR_USER_NAME'	=> $user_name,
			'USER_FLAIR_TITLE'	=> $this->language->lang('ACP_FLAIR_USER', $user_name),

			'U_ACTION'	=> $this->u_action . '&amp;user_id=' . $user_id,
			'U_BACK'	=> $this->u_action,
		));

		$this->assign_flair_tpl_vars($user_name);
		$this->assign_user_tpl_vars($user_name, $user_flair);
	}

	/**
	 * Assign template variables for the available flair.
	 *
	 * @param string $user_name The name of the user being worked on
	 */
	protected function assign_flair_tpl_vars($user_name)
	{
		$available_cats = $this->cat_operator->get_categories();
		$categories = array(array('category' => $this->language->lang('FLAIR_UNCATEGORIZED')));
		foreach ($available_cats as $entity)
		{
			$categories[$entity->get_id()]['category'] = $entity->get_name();
		}

		$flair = $this->flair_operator->get_flair();
		foreach ($flair as $entity)
		{
			$categories[$entity->get_category()]['items'][] = $entity;
		}

		foreach ($categories as $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('cat', array(
				'CAT_NAME'	=> $category['category'],
			));

			foreach ($category['items'] as $entity)
			{
				$this->template->assign_block_vars('cat.item', array(
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),

					'ADD_TITLE'	=> $this->language->lang('ACP_FLAIR_ADD_TITLE', $entity->get_name(), $user_name),
				));
			}
		}
	}

	/**
	 * Assign template variables for the user or group flair.
	 *
	 * @param string $user_name  The name of the user being worked on
	 * @param array  $user_flair The flair items assigned to the user being worked on
	 */
	protected function assign_user_tpl_vars($user_name, array $user_flair)
	{
		foreach ($user_flair as $category)
		{
			$this->template->assign_block_vars('flair', array(
				'CAT_NAME'	=> $category['category']->get_name(),
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('flair.item', array(
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),
					'FLAIR_COUNT'		=> $item['count'],

					'REMOVE_TITLE'		=> $this->language->lang('ACP_FLAIR_REMOVE_TITLE', $entity->get_name(), $user_name),
					'REMOVE_ALL_TITLE'	=> $this->language->lang('ACP_FLAIR_REMOVE_ALL_TITLE', $entity->get_name(), $user_name),
				));
			}
		}
	}

	/**
	 * Make a change to the flair assigned to the user or group being worked on.
	 *
	 * @param int    $user_id The ID of the user being worked on
	 * @param string $change  The type of change to make (add|remove|remove_all)
	 */
	protected function change_flair($user_id, $change)
	{
		if (!check_form_key('edit_user_flair'))
		{
			trigger_error('FORM_INVALID');
		}

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

		redirect($this->u_action . '&amp;user_id=' . $user_id);
	}
}
