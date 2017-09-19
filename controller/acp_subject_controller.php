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
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use stevotvr\flair\operator\category_interface;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\subject_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair user/group ACP controller base class.
 */
abstract class acp_subject_controller extends acp_base_controller
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
	 * @var \stevotvr\flair\operator\subject_interface
	 */
	protected $subject_operator;

	/**
	 * The language string for the subject flair section title.
	 *
	 * @var string
	 */
	protected $title_lang;

	/**
	 * The language string for the add links.
	 *
	 * @var string
	 */
	protected $add_lang;

	/**
	 * The language string for the remove links.
	 *
	 * @var string
	 */
	protected $remove_lang;

	/**
	 * The language string for the remove all links.
	 *
	 * @var string
	 */
	protected $remove_all_lang;

	/**
	 * @param ContainerInterface                          $container
	 * @param \phpbb\language\language                    $language
	 * @param \phpbb\request\request                      $request
	 * @param \phpbb\template\template                    $template
	 * @param \phpbb\config\config                        $config
	 * @param \phpbb\db\driver\driver_interface           $db
	 * @param \phpbb\user                                 $user
	 * @param \stevotvr\flair\operator\category_interface $cat_operator
	 * @param \stevotvr\flair\operator\flair_interface    $flair_operator
	 * @param \stevotvr\flair\operator\subject_interface  $subject_operator
	 */
	public function __construct(ContainerInterface $container, language $language, request $request, template $template, config $config, driver_interface $db, user $user, category_interface $cat_operator, flair_interface $flair_operator, subject_interface $subject_operator)
	{
		parent::__construct($container, $language, $request, $template);
		$this->config = $config;
		$this->db = $db;
		$this->user = $user;
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
		$this->subject_operator = $subject_operator;
	}

	protected function edit_flair($subject_id, $subject_name)
	{
		add_form_key('acp_flair');

		if ($this->request->is_set_post('add_flair'))
		{
			$this->change_flair($subject_id, 'add');
		}
		else if ($this->request->is_set_post('remove_flair'))
		{
			$this->change_flair($subject_id, 'remove');
		}
		else if ($this->request->is_set_post('remove_all_flair'))
		{
			$this->change_flair($subject_id, 'remove_all');
		}

		$subject_flair = $this->subject_operator->get_flair($subject_id);
		$this->assign_tpl_vars($subject_id, $subject_name, $subject_flair);
	}

	/**
	 * Assign the template variables for the page.
	 *
	 * @param int    $subject_id    The ID of the subject being worked on
	 * @param string $subject_name  The name of the subject being worked on
	 * @param array  $subject_flair The flair items assigned to the subject being worked on
	 */
	protected function assign_tpl_vars($subject_id, $subject_name, array $subject_flair)
	{
		$this->template->assign_vars(array(
			'FLAIR_SUBJECT_NAME'	=> $subject_name,
			'SUBJECT_FLAIR_TITLE'	=> $this->language->lang($this->title_lang, $subject_name),

			'U_ACTION'	=> $this->u_action . '&amp;subject_id=' . $subject_id,
			'U_BACK'	=> $this->u_action,
		));

		$this->assign_flair_tpl_vars($subject_name);
		$this->assign_subject_tpl_vars($subject_name, $subject_flair);
	}

	/**
	 * Assign template variables for the available flair.
	 *
	 * @param string $subject_name The name of the subject being worked on
	 */
	protected function assign_flair_tpl_vars($subject_name)
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

					'ADD_TITLE'	=> $this->language->lang($this->add_lang, $entity->get_name(), $subject_name),
				));
			}
		}
	}

	/**
	 * Assign template variables for the user or group flair.
	 *
	 * @param string $subject_name  The name of the subject being worked on
	 * @param array  $subject_flair The flair items assigned to the subject being worked on
	 */
	protected function assign_subject_tpl_vars($subject_name, array $subject_flair)
	{
		foreach ($subject_flair as $category)
		{
			$this->template->assign_block_vars('flair', array(
				'CAT_NAME'	=> $category['category'],
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

					'REMOVE_TITLE'		=> $this->language->lang($this->remove_lang, $entity->get_name(), $subject_name),
					'REMOVE_ALL_TITLE'	=> $this->language->lang($this->remove_all_lang, $entity->get_name(), $subject_name),
				));
			}
		}
	}

	/**
	 * Make a change to the flair assigned to the user or group being worked on.
	 *
	 * @param int    $subject_id The ID of the subject being worked on
	 * @param string $change     The type of change to make (add|remove|remove_all)
	 */
	protected function change_flair($subject_id, $change)
	{
		if (!$this->check_form_key())
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
				$this->subject_operator->set_flair_count($subject_id, $id, 0);
				return;
			}

			$counts = $this->request->variable($change . '_count', array('' => ''));
			$count = (isset($counts[$id])) ? (int) $counts[$id] : 1;

			$this->subject_operator->{$change . '_flair'}($subject_id, $id, $count);
		}

		redirect($this->u_action . '&amp;subject_id=' . $subject_id);
	}

	/**
	 * Custom form key check that ignores 0 timespans.
	 *
	 * @return bool The form key is valid
	 */
	protected function check_form_key()
	{
		if ($this->request->is_set_post('creation_time') && $this->request->is_set_post('form_token'))
		{
			$timespan = ($this->config['form_token_lifetime'] == -1) ? -1 : max(30, $this->config['form_token_lifetime']);

			$creation_time	= abs($this->request->variable('creation_time', 0));
			$token = $this->request->variable('form_token', '');

			$diff = time() - $creation_time;

			if (defined('DEBUG_TEST') || $diff >= 0 && ($diff <= $timespan || $timespan === -1))
			{
				$token_sid = ($this->user->data['user_id'] == ANONYMOUS && !empty($this->config['form_token_sid_guests'])) ? $this->user->session_id : '';
				$key = sha1($creation_time . $this->user->data['user_form_salt'] . 'acp_flair' . $token_sid);

				if ($key === $token)
				{
					return true;
				}
			}
		}

		return false;
	}
}
