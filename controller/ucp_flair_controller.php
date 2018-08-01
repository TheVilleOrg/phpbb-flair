<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\user;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair UCP controller.
 */
class ucp_flair_controller extends acp_base_controller implements ucp_flair_interface
{
	/**
	 * @var \phpbb\user
	 */
	protected $user;

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
	 * @param \phpbb\user                              $user
	 * @param \stevotvr\flair\operator\flair_interface $flair_operator
	 * @param \stevotvr\flair\operator\user_interface  $user_operator
	 */
	public function setup(user $user, flair_interface $flair_operator, user_interface $user_operator)
	{
		$this->user = $user;
		$this->flair_operator = $flair_operator;
		$this->user_operator = $user_operator;
	}

	public function edit_flair()
	{
		$user_id = (int) $this->user->data['user_id'];

		$user_flair = $this->user_operator->get_user_flair((array) $user_id);
		$user_flair = isset($user_flair[$user_id]) ? $user_flair[$user_id] : array();
		$user_flair_ids = array();
		foreach (array_column($user_flair, 'items') as $items)
		{
			$user_flair_ids = array_merge($user_flair_ids, array_keys($items));
		}

		$group_memberships = array_column(group_memberships(false, $user_id), 'group_id');
		$available_flair = $this->flair_operator->get_group_flair($group_memberships);
		$available_flair_ids = array();
		foreach ($available_flair as $cat_id => $category)
		{
			$available_flair_ids = array_merge($available_flair_ids, array_keys($category['items']));

			foreach ($category['items'] as $item_id => $item)
			{
				if (in_array($item_id, $user_flair_ids))
				{
					unset($available_flair[$cat_id]['items'][$item_id]);
				}
			}

			if (empty($available_flair[$cat_id]['items']))
			{
				unset($available_flair[$cat_id]);
			}
		}

		add_form_key('edit_user_flair');

		if ($this->request->is_set_post('add_flair'))
		{
			$this->change_flair('add', $available_flair_ids);
			return;
		}
		else if ($this->request->is_set_post('remove_flair'))
		{
			$this->change_flair('remove', $available_flair_ids);
			return;
		}

		foreach ($user_flair as $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('user_flair', array(
				'CAT_NAME'	=> $category['category']->get_name(),
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('user_flair.items', array(
					'S_IS_FREE'	=> in_array($entity->get_id(), $available_flair_ids),

					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),

					'REMOVE_TITLE'	=> $this->language->lang('UCP_FLAIR_REMOVE', $entity->get_name()),
				));
			}
		}

		foreach ($available_flair as $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('available_flair', array(
				'CAT_NAME'	=> $category['category']->get_name(),
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('available_flair.items', array(
					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),

					'ADD_TITLE'		=> $this->language->lang('UCP_FLAIR_ADD', $entity->get_name()),
				));
			}
		}
	}

	/**
	 * Make a change to the flair assigned to the user.
	 *
	 * @param string $change          The type of change to make (add|remove)
	 * @param array  $available_flair The array of available flair IDs
	 */
	protected function change_flair($change, array $available_flair)
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

		if (in_array($id, $available_flair))
		{
			$user_id = (int) $this->user->data['user_id'];

			if ($change === 'add')
			{
				$this->user_operator->set_flair_count($user_id, $id, 1, false);
			}
			else if ($change === 'remove')
			{
				$this->user_operator->set_flair_count($user_id, $id, 0, false);
			}
		}

		redirect($this->u_action);
	}
}
