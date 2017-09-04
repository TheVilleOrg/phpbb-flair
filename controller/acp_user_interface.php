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
 * Profile Flair user ACP controller interface.
 */
interface acp_user_interface
{
	/**
	 * @param string $page_url The URL for the current page
	 */
	public function set_page_url($page_url);

	/**
	 * Show the user search page.
	 */
	public function find_user();

	/**
	 * Handle the user_flair mode.
	 */
	public function edit_user_flair();
}
