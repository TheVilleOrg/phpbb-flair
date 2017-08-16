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
	 * @var \phpbb\config\config
	 */
	protected $config;

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
	 * @var array The array of error language strings
	 */
	protected $error = array();

	public function main($id, $mode)
	{
		global $phpbb_container;
		$this->config = $phpbb_container->get('config');
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');
		$this->template = $phpbb_container->get('template');

		add_form_key('stevotvr_flair_acp');

		$submit = $this->request->is_set_post('submit');
		if ($submit && !check_form_key('stevotvr_flair_acp'))
		{
			trigger_error('FORM_INVALID');
		}

		switch ($mode)
		{
			default:
				$this->settings($submit);
			break;
		}

		$error = array_map(array($this->language, 'lang'), $this->error);
		$this->template->assign_vars(array(
			'ERROR_MSG'	=> implode('<br />', $error),
			'U_ACTION'	=> $this->u_action,
			'S_ERROR'	=> count($error) > 0,
		));
	}

	/**
	 * Handle the settings mode of the module.
	 *
	 * @param boolean $submit	The form was submitted
	 */
	protected function settings($submit)
	{
		$this->tpl_name = 'settings';
		$this->page_title = 'ACP_FLAIR_SETTINGS_TITLE';

		if ($submit)
		{
			$show_on_profile = $request->variable('flair_show_on_profile', '');
			if (strlen($show_on_profile))
			{
				$config->set('stevotvr_flair_show_on_profile', $show_on_profile ? 1 : 0);
			}

			$show_on_viewtopic = $request->variable('flair_show_on_viewtopic', '');
			if (strlen($show_on_viewtopic))
			{
				$config->set('stevotvr_flair_show_on_viewtopic', $show_on_viewtopic ? 1 : 0);
			}

			trigger_error($language->lang('ACP_FLAIR_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'FLAIR_SHOW_ON_PROFILE'		=> $this->config['stevotvr_flair_show_on_profile'],
			'FLAIR_SHOW_ON_VIEWTOPIC'	=> $this->config['stevotvr_flair_show_on_viewtopic'],
		));
	}
}
