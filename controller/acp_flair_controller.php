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
use stevotvr\flair\operator\category_interface as cat_operator;
use stevotvr\flair\operator\flair_interface as flair_operator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair flair management ACP controller.
 */
class acp_flair_controller extends acp_base_controller implements acp_flair_interface
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
	 * @param ContainerInterface                          $container
	 * @param \phpbb\language\language                    $language
	 * @param \phpbb\request\request                      $request
	 * @param \phpbb\template\template                    $template
	 * @param \stevotvr\flair\operator\category_interface $cat_operator
	 * @param \stevotvr\flair\operator\flair_interface    $flair_operator
	 */
	public function __construct(ContainerInterface $container, language $language, request $request, template $template, cat_operator $cat_operator, flair_operator $flair_operator)
	{
		parent::__construct($container, $language, $request, $template);
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
	}

	public function add_flair()
	{
		$entity = $this->container->get('stevotvr.flair.entity.flair');
		$entity->set_category($this->request->variable('cat_id', 0));
		$this->add_edit_flair_data($entity);
		$this->template->assign_vars(array(
			'S_ADD_FLAIR'	=> true,

			'U_ACTION'		=> $this->u_action . '&amp;action=add&amp;cat_id=' . $entity->get_category(),
		));
	}

	public function edit_flair($flair_id)
	{
		$entity = $this->container->get('stevotvr.flair.entity.flair')->load($flair_id);
		$this->add_edit_flair_data($entity);
		$this->template->assign_vars(array(
			'S_EDIT_FLAIR'	=> true,

			'U_ACTION'		=> $this->u_action . '&amp;action=edit&amp;cat_id=' . $entity->get_category() . '&amp;flair_id=' . $flair_id,
		));
	}

	/**
	 * Process data for the add/edit flair form.
	 *
	 * @param \stevotvr\flair\entity\flair_interface $entity The flair item being processed
	 */
	protected function add_edit_flair_data(flair_entity $entity)
	{
		$errors = array();

		$submit = $this->request->is_set_post('submit');

		add_form_key('add_edit_flair');

		$data = array(
			'category'		=> $this->request->variable('flair_category', 0),
			'name'			=> $this->request->variable('flair_name', '', true),
			'desc'			=> $this->request->variable('flair_desc', '', true),
			'color'			=> $this->request->variable('flair_color', ''),
			'icon'			=> $this->request->variable('flair_icon', ''),
			'icon_color'	=> $this->request->variable('flair_icon_color', ''),
			'font_color'	=> $this->request->variable('flair_font_color', ''),
		);

		$this->set_parse_options($entity, $submit);

		if ($submit)
		{
			if (!check_form_key('add_edit_flair', -1))
			{
				$errors[] = 'FORM_INVALID';
			}

			if ($data['color'] === '' && $data['icon'] === '')
			{
				$errors[] = 'ACP_ERROR_APPEARANCE_REQUIRED';
			}

			foreach ($data as $name => $value)
			{
				try
				{
					$entity->{'set_' . $name}($value);
				}
				catch (base $e)
				{
					$errors[] = $e->get_message($this->language);
				}
			}

			if (empty($errors))
			{
				if ($entity->get_id())
				{
					$entity->save();
					$message = 'ACP_FLAIR_EDIT_SUCCESS';
				}
				else
				{
					$entity = $this->flair_operator->add_flair($entity);
					$message = 'ACP_FLAIR_ADD_SUCCESS';
				}

				trigger_error($this->language->lang($message) . adm_back_link($this->u_action . '&amp;cat_id=' . $entity->get_category()));
			}
		}

		$errors = array_map(array($this->language, 'lang'), $errors);

		$this->template->assign_vars(array(
			'S_ERROR'	=> (bool) count($errors),
			'ERROR_MSG'	=> count($errors) ? implode('<br />', $errors) : '',

			'FLAIR_CATEGORY'	=> $entity->get_category(),
			'FLAIR_NAME'		=> $entity->get_name(),
			'FLAIR_DESC'		=> $entity->get_desc_for_edit(),
			'FLAIR_COLOR'		=> $entity->get_color(),
			'FLAIR_ICON'		=> $entity->get_icon(),
			'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
			'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),

			'S_PARSE_BBCODE_CHECKED'	=> $entity->is_bbcode_enabled(),
			'S_PARSE_SMILIES_CHECKED'	=> $entity->is_smilies_enabled(),
			'S_PARSE_MAGIC_URL_CHECKED'	=> $entity->is_magic_url_enabled(),

			'U_BACK'	=> $this->u_action . '&amp;cat_id=' . $entity->get_category(),
		));

		$this->load_cat_select_data($entity->get_category());
	}

	/**
	 * Process parsing options for the flair description field.
	 *
	 * @param \stevotvr\flair\entity\flair_interface $entity The flair item being processed
	 * @param boolean                                $submit The form has been submitted
	 */
	protected function set_parse_options(flair_entity $entity, $submit)
	{
		$bbcode = $this->request->variable('parse_bbcode', false);
		$magic_url = $this->request->variable('parse_magic_url', false);
		$smilies = $this->request->variable('parse_smilies', false);

		$parse_options = array(
			'bbcode'	=> $submit ? $bbcode : ($entity->get_id() ? $entity->is_bbcode_enabled() : 1),
			'magic_url'	=> $submit ? $magic_url : ($entity->get_id() ? $entity->is_magic_url_enabled() : 1),
			'smilies'	=> $submit ? $smilies : ($entity->get_id() ? $entity->is_smilies_enabled() : 1),
		);

		foreach ($parse_options as $function => $enabled)
		{
			$entity->{'set_' . $function . '_enabled'}($enabled);
		}
	}

	/**
	 * Load the template data for the category select box.
	 *
	 * @param int $selected The ID of the selected item
	 */
	protected function load_cat_select_data($selected)
	{
		$categories = $this->cat_operator->get_categories();
		if (!count($categories))
		{
			return;
		}

		foreach ($categories as $category)
		{
			$this->template->assign_block_vars('cats', array(
				'CAT_ID'	=> $category->get_id(),
				'CAT_NAME'	=> $category->get_name(),

				'S_SELECTED'	=> $category->get_id() === $selected,
			));
		}
	}

	public function delete_flair($flair_id)
	{
		$entity = $this->container->get('stevotvr.flair.entity.flair')->load($flair_id);

		if (!confirm_box(true))
		{
			$hidden_fields = build_hidden_fields(array(
				'flair_id'	=> $flair_id,
				'cat_id'	=> $entity->get_category(),
				'mode'		=> 'manage',
				'action'	=> 'delete',
			));
			confirm_box(false, $this->language->lang('ACP_FLAIR_DELETE_FLAIR_CONFIRM'), $hidden_fields);
			return;
		}

		try
		{
			$this->flair_operator->delete_flair($flair_id);
		}
		catch (base $e)
		{
			trigger_error($this->language->lang('ACP_FLAIR_DELETE_ERRORED') . adm_back_link($this->u_action . '&amp;cat_id=' . $entity->get_category()), E_USER_WARNING);
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

		trigger_error($this->language->lang('ACP_FLAIR_DELETE_SUCCESS') . adm_back_link($this->u_action . '&amp;cat_id=' . $entity->get_category()));
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
}
