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

/**
 * Profile Flair user/group flair management ACP controller interface.
 */
interface acp_subject_interface extends acp_base_interface
{
	/**
	 * Show the subject selection page.
	 */
	public function select_subject();

	/**
	 * Handle the edit mode.
	 */
	public function edit_subject_flair();
}
