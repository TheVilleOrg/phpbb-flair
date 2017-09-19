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
 * Profile Flair group ACP module info.
 */
class group_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\flair\acp\group_module',
			'title'		=> 'ACP_FLAIR_MANAGE_GROUPS',
			'modes'		=> array(
				'main'	=> array(
					'title'	=> 'ACP_FLAIR_MANAGE_GROUPS',
					'auth'	=> 'ext_stevotvr/flair && acl_a_manage_flair',
					'cat'	=> array('ACP_FLAIR_MANAGE_GROUPS'),
				),
			),
		);
	}
}
