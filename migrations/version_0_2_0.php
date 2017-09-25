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
 * Profile Flair migration for version 0.2.0.
 */
class version_0_2_0 extends migration
{
	static public function depends_on()
	{
		return array('\stevotvr\flair\migrations\version_0_1_0');
	}

	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'flair_groups' => array(
					'COLUMNS' => array(
						'group_id'		=> array('UINT', 0),
						'flair_id'		=> array('UINT', 0),
					),
					'PRIMARY_KEY' => array('flair_id', 'group_id'),
				),
				$this->table_prefix . 'flair_triggers' => array(
					'COLUMNS' => array(
						'flair_id'		=> array('UINT', 0),
						'trig_name'		=> array('VCHAR', ''),
						'trig_value'	=> array('UINT', 0),
					),
					'PRIMARY_KEY' => array('flair_id', 'trig_value'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'   => array(
				$this->table_prefix . 'flair_groups',
				$this->table_prefix . 'flair_triggers',
			),
		);
	}
}
