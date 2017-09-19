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
use stevotvr\flair\operator\user_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair user ACP controller.
 */
class acp_user_controller extends acp_subject_controller implements acp_subject_interface
{
	protected $title_lang		= 'ACP_FLAIR_USER';
	protected $add_lang			= 'ACP_FLAIR_ADD_TITLE';
	protected $remove_lang		= 'ACP_FLAIR_REMOVE_TITLE';
	protected $remove_all_lang	= 'ACP_FLAIR_REMOVE_ALL_TITLE';

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
	 * @param ContainerInterface                          $container
	 * @param \phpbb\language\language                    $language
	 * @param \phpbb\request\request                      $request
	 * @param \phpbb\template\template                    $template
	 * @param \phpbb\config\config                        $config
	 * @param \phpbb\db\driver\driver_interface           $db
	 * @param \phpbb\user                                 $user
	 * @param \stevotvr\flair\operator\category_interface $cat_operator
	 * @param \stevotvr\flair\operator\flair_interface    $flair_operator
	 * @param \stevotvr\flair\operator\user_interface     $user_operator
	 * @param string                                      $root_path      The root phpBB path
	 * @param string                                      $php_ext        The script file extension
	 */
	public function __construct(ContainerInterface $container, language $language, request $request, template $template, config $config, driver_interface $db, user $user, category_interface $cat_operator, flair_interface $flair_operator, user_interface $user_operator, $root_path, $php_ext)
	{
		parent::__construct($container, $language, $request, $template, $config, $db, $user, $cat_operator, $flair_operator, $user_operator);
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	public function select_subject()
	{
		$this->language->add_lang('acp/users');

		$u_find_username = append_sid($this->root_path . 'memberlist.' . $this->php_ext,
			'mode=searchuser&amp;form=select_user&amp;field=username&amp;select_single=true');

		$this->template->assign_vars(array(
			'S_SELECT_USER'		=> true,

			'ANONYMOUS_USER_ID'	=> ANONYMOUS,

			'U_ACTION'			=> $this->u_action,
			'U_FIND_USERNAME'	=> $u_find_username,
		));
	}

	public function edit_subject_flair()
	{
		$user_id = $this->request->variable('subject_id', 0);
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

		$this->edit_flair($user_id, $userrow['username']);
	}
}
