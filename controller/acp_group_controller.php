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
use phpbb\group\helper;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use stevotvr\flair\operator\category_interface;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\group_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair group ACP controller.
 */
class acp_group_controller extends acp_subject_controller implements acp_subject_interface
{
	/**
	 * @var \phpbb\group\helper
	 */
	protected $group_helper;

	protected $title_lang		= 'ACP_FLAIR_GROUP';
	protected $add_lang			= 'ACP_FLAIR_GROUP_ADD_TITLE';
	protected $remove_lang		= 'ACP_FLAIR_GROUP_REMOVE_TITLE';
	protected $remove_all_lang	= 'ACP_FLAIR_GROUP_REMOVE_ALL_TITLE';

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
	 * @param \stevotvr\flair\operator\group_interface    $group_operator
	 * @param \phpbb\group\helper                         $group_helper
	 */
	public function __construct(ContainerInterface $container, language $language, request $request, template $template, config $config, driver_interface $db, user $user, category_interface $cat_operator, flair_interface $flair_operator, group_interface $group_operator, $group_helper)
	{
		parent::__construct($container, $language, $request, $template, $config, $db, $user, $cat_operator, $flair_operator, $group_operator);
		$this->group_helper = $group_helper;
	}

	public function select_subject()
	{
		$this->language->add_lang('acp/groups');

		$sql = 'SELECT group_id, group_name
				FROM ' . GROUPS_TABLE . '
				ORDER BY group_name ASC';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->template->assign_block_vars('group', array(
				'GROUP_ID'		=> $row['group_id'],
				'GROUP_NAME'	=> $this->group_helper->get_name($row['group_name']),
			));
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'S_SELECT_GROUP'	=> true,

			'U_ACTION'	=> $this->u_action,
		));
	}

	public function edit_subject_flair()
	{
		$group_id = $this->request->variable('subject_id', 0);

		$sql = 'SELECT group_name
				FROM ' . GROUPS_TABLE . '
				WHERE group_id = ' . (int) $group_id;
		$result = $this->db->sql_query($sql);
		$grouprow = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$grouprow)
		{
			trigger_error($this->language->lang('NO_GROUP') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->edit_flair($group_id, $this->group_helper->get_name($grouprow['group_name']));
	}
}
