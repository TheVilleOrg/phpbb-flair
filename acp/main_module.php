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
 * Profile Flair ACP module.
 */
class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\request\request
	 */
	protected $request;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$this->container = $phpbb_container;
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		switch ($mode)
		{
			case 'manage':
				$this->manage();
			break;
			default:
				$this->settings();
			break;
		}
	}

	/**
	 * Handle the settings mode of the module.
	 */
	protected function settings()
	{
		$this->tpl_name = 'settings';
		$this->page_title = 'ACP_FLAIR_SETTINGS_TITLE';

		$config = $this->container->get('config');
		$template = $this->container->get('template');

		add_form_key('stevotvr_flair_settings');

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('stevotvr_flair_settings'))
			{
				trigger_error('FORM_INVALID');
			}

			$show_on_profile = $this->request->variable('flair_show_on_profile', '');
			if (strlen($show_on_profile))
			{
				$config->set('stevotvr_flair_show_on_profile', $show_on_profile ? 1 : 0);
			}

			$show_on_viewtopic = $this->request->variable('flair_show_on_viewtopic', '');
			if (strlen($show_on_viewtopic))
			{
				$config->set('stevotvr_flair_show_on_viewtopic', $show_on_viewtopic ? 1 : 0);
			}

			trigger_error($this->language->lang('ACP_FLAIR_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'FLAIR_SHOW_ON_PROFILE'		=> $config['stevotvr_flair_show_on_profile'],
			'FLAIR_SHOW_ON_VIEWTOPIC'	=> $config['stevotvr_flair_show_on_viewtopic'],

			'U_ACTION'	=> $this->u_action,
		));
	}

	/**
	 * Handle the manage mode of the module.
	 */
	protected function manage()
	{
		$this->tpl_name = 'manage';

		$controller = $this->container->get('stevotvr.flair.admin.controller');
		$controller->set_page_url($this->u_action);

		$action = $this->request->variable('action', '');
		$flair_id = $this->request->variable('flair_id', 0);

		switch ($action)
		{
			case 'add_cat':
				$this->page_title = 'ACP_FLAIR_ADD_CAT';
				$controller->add_cat();
				return;
			break;
			case 'edit_cat':
				$this->page_title = 'ACP_FLAIR_EDIT_CAT';
				$controller->edit_flair($flair_id);
				return;
			break;
			case 'delete_cat':
				$this->page_title = 'ACP_FLAIR_DELETE_CAT';
				$controller->delete_cat($flair_id);
				return;
			break;
			case 'add':
				$this->page_title = 'ACP_FLAIR_ADD';
				$controller->add_flair();
				return;
			break;
			case 'edit':
				$this->page_title = 'ACP_FLAIR_EDIT';
				$controller->edit_flair($flair_id);
				return;
			break;
			case 'delete':
				$controller->delete_flair($flair_id);
			break;
			case 'move_up':
				$controller->move_flair($flair_id, -1);
			break;
			case 'move_down':
				$controller->move_flair($flair_id, 1);
			break;
		}

		$this->page_title = 'ACP_FLAIR_MANAGE';
		$controller->display_flair();
	}
}
