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

use stevotvr\flair\operator\category_interface;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair user MCP controller.
 */
class mcp_user_controller extends acp_base_controller implements mcp_user_interface
{
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
	 * Set up the controller.
	 *
	 * @param \stevotvr\flair\operator\category_interface $cat_operator
	 * @param \stevotvr\flair\operator\flair_interface    $flair_operator
	 * @param \stevotvr\flair\operator\user_interface     $user_operator
	 */
	public function setup(category_interface $cat_operator, flair_interface $flair_operator, user_interface $user_operator)
	{
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
		$this->user_operator = $user_operator;
	}

	public function find_user()
	{
		$this->language->add_lang('acp/users');

		$u_find_username = append_sid($this->root_path . 'memberlist.' . $this->php_ext,
			'mode=searchuser&amp;form=select_user&amp;field=username&amp;select_single=true');

		$this->template->assign_vars(array(
			'S_SELECT_USER'		=> true,

			'U_ACTION'			=> str_replace('mode=front', 'mode=user_flair', $this->u_action),
			'U_FIND_USERNAME'	=> $u_find_username,
		));
	}

	public function edit_user_flair(array $userrow)
	{
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
		else if ($this->request->is_set_post('set_flair'))
		{
			$this->change_flair($user_id, 'set');
		}

		$user_flair = $this->user_operator->get_user_flair((array) $user_id);
		$user_flair = isset($user_flair[$user_id]) ? $user_flair[$user_id] : array();
		$this->assign_tpl_vars($user_id, $userrow['username'], $userrow['user_colour'], $user_flair);
	}

	/**
	 * Assign the template variables for the page.
	 *
	 * @param int    $user_id     The ID of the user being worked on
	 * @param string $username    The name of the user being worked on
	 * @param string $user_colour The color of the user being worked on
	 * @param array  $user_flair  The flair items assigned to the user being worked on
	 */
	protected function assign_tpl_vars($user_id, $username, $user_colour, array $user_flair)
	{
		$this->template->assign_vars(array(
			'USER_FLAIR_TITLE'	=> $this->language->lang('MCP_FLAIR_USER_FLAIR', get_username_string('full', $user_id, $username, $user_colour)),

			'U_ACTION'	=> $this->u_action . '&amp;u=' . $user_id,
		));

		$this->assign_flair_tpl_vars($username);
		$this->assign_user_tpl_vars($username, $user_flair);
	}

	/**
	 * Assign template variables for the available flair.
	 *
	 * @param string $username The name of the user being worked on
	 */
	protected function assign_flair_tpl_vars($username)
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
					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),

					'ADD_TITLE'	=> $this->language->lang('MCP_FLAIR_ADD_TITLE', $entity->get_name(), $username),
				));
			}
		}
	}

	/**
	 * Assign template variables for the user flair.
	 *
	 * @param string $username   The name of the user being worked on
	 * @param array  $user_flair The flair items assigned to the user being worked on
	 */
	protected function assign_user_tpl_vars($username, array $user_flair)
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
					'S_FROM_GROUP'	=> $item['from_group'],

					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),
					'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),
					'FLAIR_COUNT'		=> $item['count'],

					'SET_COUNT_TITLE'	=> $this->language->lang('MCP_FLAIR_SET_COUNT_TITLE', $entity->get_name(), $username),
					'REMOVE_TITLE'		=> $this->language->lang('MCP_FLAIR_REMOVE_TITLE', $entity->get_name(), $username),
				));
			}
		}
	}

	/**
	 * Make a change to the flair assigned to the user or group being worked on.
	 *
	 * @param int    $user_id The ID of the user being worked on
	 * @param string $change  The type of change to make (add|remove|set)
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
			if ($change === 'remove')
			{
				$this->user_operator->set_flair_count($user_id, $id, 0);
				return;
			}

			$counts = $this->request->variable($change . '_count', array('' => ''));
			$count = (isset($counts[$id])) ? (int) $counts[$id] : 1;

			if ($change === 'add')
			{
				$this->user_operator->add_flair($user_id, $id, $count);
			}
			else if ($change === 'set')
			{
				$this->user_operator->set_flair_count($user_id, $id, $count);
			}
		}

		redirect($this->u_action . '&amp;u=' . $user_id);
	}
}
