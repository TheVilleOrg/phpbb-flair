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
						'flair_id'						=> array('UINT', null, 'auto_increment'),
						'flair_category'				=> array('UINT', 0),
						'flair_name'					=> array('VCHAR_UNI', ''),
						'flair_desc'					=> array('TEXT_UNI', ''),
						'flair_desc_bbcode_uid'			=> array('VCHAR:8', ''),
						'flair_desc_bbcode_bitfield'	=> array('VCHAR:255', ''),
						'flair_desc_bbcode_options'		=> array('UINT:11', 7),
						'flair_order'					=> array('UINT', 0),
						'flair_color'					=> array('VCHAR:6', ''),
						'flair_icon'					=> array('VCHAR:50', ''),
						'flair_icon_color'				=> array('VCHAR:6', ''),
						'flair_font_color'				=> array('VCHAR:6', ''),
					),
					'PRIMARY_KEY' => 'flair_id',
					'KEYS' => array(
						'flr_parent'	=> array('INDEX', 'flair_category'),
						'flr_order'		=> array('INDEX', 'flair_order'),
					),
				),
				$this->table_prefix . 'flair_categories' => array(
					'COLUMNS' => array(
						'cat_id'				=> array('UINT', null, 'auto_increment'),
						'cat_name'				=> array('VCHAR_UNI', ''),
						'cat_order'				=> array('UINT', 0),
						'cat_display_profile'	=> array('BOOL', 1),
						'cat_display_posts'		=> array('BOOL', 1),
					),
					'PRIMARY_KEY' => 'cat_id',
					'KEYS' => array(
						'ct_order'		=> array('INDEX', 'cat_order'),
						'ct_profile'	=> array('INDEX', 'cat_display_profile'),
						'ct_posts'		=> array('INDEX', 'cat_display_posts'),
					),
				),
				$this->table_prefix . 'flair_users' => array(
					'COLUMNS' => array(
						'user_id'		=> array('UINT', 0),
						'flair_id'		=> array('UINT', 0),
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
				$this->table_prefix . 'flair_categories',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('stevotvr_flair_show_on_profile', 1)),
			array('config.add', array('stevotvr_flair_show_on_posts', 1)),

			array('permission.add', array('a_manage_flair', true, 'a_user')),

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
			array('module.add', array(
				'acp',
				'ACP_CAT_USERS',
				array(
					'module_basename'	=> '\stevotvr\flair\acp\user_module',
					'modes'				=> array('main'),
				),
			)),
		);
	}
}
