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
				$this->table_prefix . 'flair_categories' => array(
					'COLUMNS' => array(
						'flair_cat_id'		=> array('UINT', null, 'auto_increment'),
						'flair_cat_ident'	=> array('VCHAR:20', ''),
						'flair_cat_name'	=> array('VCHAR_UNI', ''),
						'flair_cat_order'	=> array('UINT', 0),
					),
					'PRIMARY_KEY' => 'flair_cat_id',
					'KEYS' => array(
						'flr_cat_order'	=> array('INDEX', 'flair_cat_order'),
					),
				),
				$this->table_prefix . 'flair_icons' => array(
					'COLUMNS' => array(
						'flair_icon_id'		=> array('UINT', null, 'auto_increment'),
						'flair_icon_file'	=> array('VCHAR:50', ''),
						'flair_icon_width'	=> array('USINT', 0),
						'flair_icon_height'	=> array('USINT', 0),
					),
					'PRIMARY_KEY' => 'flair_icon_id',
				),
				$this->table_prefix . 'flair' => array(
					'COLUMNS' => array(
						'flair_id'		=> array('UINT', null, 'auto_increment'),
						'flair_cat_id'	=> array('UINT', 0),
						'flair_ident'	=> array('VCHAR:20', ''),
						'flair_name'	=> array('VCHAR_UNI', ''),
						'flair_desc'	=> array('TEXT_UNI', ''),
						'flair_order'	=> array('UINT', 0),
						'flair_icon'	=> array('UINT', 0),
						'flair_color'	=> array('VCHAR:6', ''),
					),
					'PRIMARY_KEY' => 'flair_id',
					'KEYS' => array(
						'flr_cat_id'	=> array('INDEX', 'flair_cat_id'),
						'flr_order'		=> array('INDEX', 'flair_order'),
					),
				),
				$this->table_prefix . 'flair_users' => array(
					'COLUMNS' => array(
						'flair_id'		=> array('UINT', 0),
						'user_id'		=> array('UINT', 0),
						'flair_count'	=> array('USINT', 1),
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
				$this->table_prefix . 'flair_icons',
				$this->table_prefix . 'flair_categories',
			),
		);
	}
}
