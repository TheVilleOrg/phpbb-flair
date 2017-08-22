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

use phpbb\json_response;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use stevotvr\flair\entity\flair_interface as flair_entity;
use stevotvr\flair\exception\base;
use stevotvr\flair\operator\flair_interface as flair_operator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair admin controller.
 */
class admin_controller implements admin_interface
{
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

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
	 * The URL for the current page.
	 *
	 * @var string
	 */
	protected $u_action;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface	$container
	 * @param \stevotvr\flair\operator\flair_interface					$flair_operator
	 * @param \phpbb\language\language									$language
	 * @param \phpbb\request\request									$request
	 * @param \phpbb\template\template									$template
	 */
	public function __construct(ContainerInterface $container, flair_operator $flair_operator, language $language, request $request, template $template)
	{
		$this->container = $container;
		$this->flair_operator = $flair_operator;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
	}

	public function set_page_url($page_url)
	{
		$this->u_action = $page_url;
	}

	public function add_cat()
	{
		$entity = $this->container->get('stevotvr.flair.entity');
		$entity->set_category(true);
		$this->add_edit_flair_data($entity);
		$this->template->assign_vars(array(
			'S_ADD_CAT'	=> true,
			'U_ACTION'	=> $this->u_action . '&amp;action=add_cat',
		));
	}

	public function delete_cat($cat_id)
	{
		$errors = array();

		add_form_key('delete_cat');

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('delete_cat'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			$action_flair = $this->request->variable('action_flair', '');
			$flair_to_cat = $this->request->variable('flair_to_cat', 0);

			try
			{
				if ($action_flair === 'delete')
				{
					$this->flair_operator->delete_all_flair($cat_id);
				}
				else
				{
					$this->flair_operator->reassign_flair($cat_id, $flair_to_cat);
				}

				$this->flair_operator->delete_flair($cat_id);
			}
			catch (base $e)
			{
				trigger_error($this->language->lang('ACP_FLAIR_CATS_DELETE_ERRORED') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			trigger_error($this->language->lang('ACP_FLAIR_CATS_DELETE_SUCCESS') . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> (bool) count($errors),
			'ERROR_MSG'	=> count($errors) ? implode('<br />', $errors) : '',

			'S_DELETE_CAT'	=> true,
			'S_HAS_FLAIR'	=> true,

			'CAT_ID'	=> $cat_id,

			'U_ACTION'	=> $this->u_action . '&amp;action=delete_cat&amp;flair_id=' . $cat_id,
			'U_BACK'	=> $this->u_action,
		));

		$categories = $this->flair_operator->get_flair(-1, false, true);
		foreach ($categories as $category)
		{
			if ($category->get_id() === $cat_id)
			{
				$this->template->assign_var('CAT_NAME', $category->get_name());
				continue;
			}

			$this->template->assign_block_vars('cats', array(
				'CAT_ID'	=> $category->get_id(),
				'CAT_NAME'	=> $category->get_name(),
			));
		}
	}

	public function display_flair()
	{
		$parent_id = $this->request->variable('parent_id', 0);
		$in_cat = $parent_id > 0;
		$entities = $this->flair_operator->get_flair($parent_id, $in_cat);

		foreach ($entities as $entity)
		{
			$vars = array(
				'FLAIR_NAME'	=> $entity->get_name(),

				'U_MOVE_UP'		=> $this->u_action . '&amp;action=move_up&amp;parent_id=' . $parent_id . '&amp;flair_id=' . $entity->get_id(),
				'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move_down&amp;parent_id=' . $parent_id . '&amp;flair_id=' . $entity->get_id(),
			);

			if ($entity->is_category())
			{
				$this->template->assign_block_vars('cats', $vars + array(
					'U_FLAIR'	=> $this->u_action . '&amp;parent_id=' . $entity->get_id(),
					'U_EDIT'	=> $this->u_action . '&amp;action=edit_cat&amp;flair_id=' . $entity->get_id(),
					'U_DELETE'	=> $this->u_action . '&amp;action=delete_cat&amp;flair_id=' . $entity->get_id(),
				));
				continue;
			}

			$this->template->assign_block_vars('flair', $vars + array(
				'FLAIR_COLOR'	=> $entity->get_color(),

				'U_EDIT'	=> $this->u_action . '&amp;action=edit&amp;flair_id=' . $entity->get_id(),
				'U_DELETE'	=> $this->u_action . '&amp;action=delete&amp;flair_id=' . $entity->get_id(),
			));
		}

		if ($in_cat)
		{
			$cat_name = $this->container->get('stevotvr.flair.entity')->load($parent_id)->get_name();
		}
		else
		{
			$cat_name = $this->language->lang('ACP_FLAIR_NO_CAT');
		}

		$this->template->assign_vars(array(
			'S_IN_CAT'	=> $in_cat,

			'CAT_NAME'	=> $cat_name,

			'U_ACTION'		=> $this->u_action,
			'U_ADD_CAT'		=> $this->u_action . '&amp;action=add_cat',
			'U_ADD_FLAIR'	=> $this->u_action . '&amp;action=add&amp;parent_id=' . $parent_id,
		));
	}

	public function add_flair()
	{
		$entity = $this->container->get('stevotvr.flair.entity');
		$entity->set_parent($this->request->variable('parent_id', 0));
		$this->add_edit_flair_data($entity);
		$this->template->assign_vars(array(
			'S_ADD_FLAIR'	=> true,
			'U_ACTION'		=> $this->u_action . '&amp;action=add&amp;parent_id=' . $entity->get_parent(),
		));
	}

	public function edit_flair($flair_id)
	{
		$entity = $this->container->get('stevotvr.flair.entity')->load($flair_id);
		$this->add_edit_flair_data($entity);

		if ($entity->is_category())
		{
			$this->template->assign_vars(array(
				'S_EDIT_CAT'	=> true,
				'U_ACTION'		=> $this->u_action . '&amp;action=edit_cat&amp;flair_id=' . $flair_id,
			));
			return;
		}

		$this->template->assign_vars(array(
			'S_EDIT_FLAIR'	=> true,
			'U_ACTION'		=> $this->u_action . '&amp;action=edit&amp;parent_id=' . $entity->get_parent() . '&amp;flair_id=' . $flair_id,
		));
	}

	protected function add_edit_flair_data(flair_entity $entity)
	{
		$errors = array();
		$submit = $this->request->is_set_post('submit');
		add_form_key('add_edit_flair');
		if ($submit)
		{
			if (!check_form_key('add_edit_flair', -1))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			try
			{
				$entity->set_parent($this->request->variable('flair_parent', 0));
				$entity->set_name($this->request->variable('flair_name', '', true));
				$entity->set_desc($this->request->variable('flair_desc', '', true));
				$entity->set_color($this->request->variable('flair_color', '', true));
			}
			catch (base $e)
			{
				$errors[] = $e->get_message($this->language);
			}

			if (empty($errors))
			{
				if ($entity->get_id())
				{
					$entity->save();
					$message = $entity->is_category() ? 'ACP_FLAIR_CATS_EDIT_SUCCESS' : 'ACP_FLAIR_EDIT_SUCCESS';
				}
				else
				{
					$entity = $this->flair_operator->add_flair($entity);
					$message = $entity->is_category() ? 'ACP_FLAIR_CATS_ADD_SUCCESS' : 'ACP_FLAIR_ADD_SUCCESS';
				}

				trigger_error($this->language->lang($message) . adm_back_link($this->u_action . '&amp;parent_id=' . $entity->get_parent()));
			}
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> (bool) count($errors),
			'ERROR_MSG'	=> count($errors) ? implode('<br />', $errors) : '',

			'FLAIR_PARENT'	=> $entity->get_parent(),
			'FLAIR_NAME'	=> $entity->get_name(),
			'FLAIR_DESC'	=> $entity->get_desc(),
			'FLAIR_COLOR'	=> $entity->get_color(),

			'U_BACK'	=> $this->u_action . '&amp;parent_id=' . $entity->get_parent(),
		));

		if (!$entity->is_category())
		{
			$this->load_cat_select_data($entity->get_parent());
		}
	}

	public function delete_flair($flair_id)
	{
		$entity = $this->container->get('stevotvr.flair.entity')->load($flair_id);

		try
		{
			$this->flair_operator->delete_flair($flair_id);
		}
		catch (base $e)
		{
			trigger_error($this->language->lang('ACP_FLAIR_DELETE_ERRORED') . adm_back_link($this->u_action . '&amp;parent_id=' . $entity->get_parent()), E_USER_WARNING);
		}

		if ($this->request->is_ajax())
		{
			$json_response = new json_response();
			$json_response->send(array(
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('ACP_FLAIR_DELETE_SUCCESS'),
				'REFRESH_DATA'	=> array(
					'time'	=> 3
				),
			));
		}

		trigger_error($this->language->lang('ACP_FLAIR_DELETE_SUCCESS') . adm_back_link($this->u_action . '&amp;parent_id=' . $entity->get_parent()));
	}

	public function move_flair($flair_id, $offset)
	{
		$this->flair_operator->move_flair($flair_id, $offset);

		if ($this->request->is_ajax())
		{
			$json_response = new json_response();
			$json_response->send(array('success' => true));
		}
	}

	/**
	 * Load the template data for the category select box.
	 *
	 * @param int	$selected	The ID of the selected item
	 */
	protected function load_cat_select_data($selected)
	{
		$categories = $this->flair_operator->get_flair(0, false, true);
		if (!count($categories))
		{
			return;
		}

		foreach ($categories as $category)
		{
			if ($category->get_id() === $selected)
			{
				$this->template->assign_block_var('cats', 'S_SELECTED', true);
			}

			$this->template->assign_block_vars('cats', array(
				'CAT_ID'	=> $category->get_id(),
				'CAT_NAME'	=> $category->get_name(),
			));
		}
	}
}
