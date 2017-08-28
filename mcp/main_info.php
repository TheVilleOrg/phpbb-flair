<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\mcp;

/**
 * Profile Flair MCP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\flair\mcp\main_module',
			'title'		=> 'MCP_FLAIR_TITLE',
			'modes'		=> array(
				'front'	=> array(
					'title'	=> 'MCP_FLAIR_FRONT',
					'auth'	=> 'ext_stevotvr/flair',
					'cat'	=> array('MCP_FLAIR_TITLE'),
				),
				'user_flair'	=> array(
					'title'	=> 'MCP_FLAIR_USER',
					'auth'	=> 'ext_stevotvr/flair',
					'cat'	=> array('MCP_FLAIR_TITLE'),
				),
			),
		);
	}
}
