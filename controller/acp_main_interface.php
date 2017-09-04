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
 * Profile Flair main ACP controller interface.
 */
interface acp_main_interface
{
	/**
	 * @param string $page_url The URL for the current page
	 */
	public function set_page_url($page_url);

	/**
	 * Add a category.
	 */
	public function add_cat();

	/**
	 * Delete a category.
	 *
	 * @param int $cat_id The database ID of the category
	 */
	public function delete_cat($cat_id);

	/**
	 * Display all flair items.
	 */
	public function display_flair();

	/**
	 * Add a flair item.
	 */
	public function add_flair();

	/**
	 * Edit a flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 */
	public function edit_flair($flair_id);

	/**
	 * Delete a flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 */
	public function delete_flair($flair_id);

	/**
	 * Move a flair item in the sorting order.
	 *
	 * @param int $flair_id The database ID of the flair item
	 * @param int $offset   The offset by which to move the flair item
	 */
	public function move_flair($flair_id, $offset);
}
