<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\migrations;

use phpbb\db\migration\migration;

/**
 * Profile Flair migration for version 1.2.0.
 */
class version_1_2_0 extends migration
{
	static public function depends_on()
	{
		return array('\stevotvr\flair\migrations\version_1_1_1');
	}

	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'flair_notif' => array(
					'COLUMNS' => array(
						'notification_id'	=> array('UINT', null, 'auto_increment'),
						'user_id'			=> array('UINT', 0),
						'flair_id'			=> array('UINT', 0),
						'flair_name'		=> array('VCHAR_UNI', ''),
						'old_count'			=> array('UINT', 0),
						'new_count'			=> array('UINT', 0),
						'updated'			=> array('UINT:11', 0),
					),
					'PRIMARY_KEY' => 'notification_id',
					'KEYS' => array(
						'u_f'	=> array('UNIQUE', array('user_id', 'flair_id')),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'   => array(
				$this->table_prefix . 'flair_notif',
			),
		);
	}

	public function update_data()
	{
		return array(
			array('config.add', array('stevotvr_flair_notify_users', 1)),
			array('config.add', array('stevotvr_flair_cron_last_run', 0)),
		);
	}
}
