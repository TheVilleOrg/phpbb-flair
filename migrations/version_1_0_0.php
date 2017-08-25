<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\migrations;

use phpbb\db\migration\migration;

/**
 * Profile Flair migration for version 1.0.0.
 */
class version_1_0_0 extends migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'flair' => array(
					'COLUMNS' => array(
						'flair_id'				=> array('UINT', null, 'auto_increment'),
						'flair_is_cat'			=> array('BOOL', 0),
						'flair_parent'			=> array('UINT', 0),
						'flair_name'			=> array('VCHAR_UNI', ''),
						'flair_desc'			=> array('TEXT_UNI', ''),
						'flair_order'			=> array('UINT', 0),
						'flair_color'			=> array('VCHAR:6', ''),
						'flair_icon'			=> array('VCHAR:50', ''),
						'flair_icon_color'		=> array('VCHAR:6', ''),
						'flair_display_profile'	=> array('BOOL', 1),
						'flair_display_posts'	=> array('BOOL', 1),
					),
					'PRIMARY_KEY' => 'flair_id',
					'KEYS' => array(
						'flr_is_cat'	=> array('INDEX', 'flair_is_cat'),
						'flr_parent'	=> array('INDEX', 'flair_parent'),
						'flr_order'		=> array('INDEX', 'flair_order'),
						'flr_profile'	=> array('INDEX', 'flair_display_profile'),
						'flr_posts'		=> array('INDEX', 'flair_display_posts'),
					),
				),
				$this->table_prefix . 'flair_users' => array(
					'COLUMNS' => array(
						'flair_id'		=> array('UINT', 0),
						'user_id'		=> array('UINT', 0),
						'flair_count'	=> array('UINT', 1),
					),
					'PRIMARY_KEY' => array('flair_id', 'user_id'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'   => array(
				$this->table_prefix . 'flair_users',
				$this->table_prefix . 'flair',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('stevotvr_flair_show_on_profile', 1)),
			array('config.add', array('stevotvr_flair_show_on_posts', 1)),
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_FLAIR_TITLE',
			)),
			array('module.add', array(
				'acp',
				'ACP_FLAIR_TITLE',
				array(
					'module_basename'	=> '\stevotvr\flair\acp\main_module',
					'modes'				=> array('settings', 'manage'),
				),
			)),
		);
	}
}
