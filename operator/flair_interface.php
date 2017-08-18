<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\operator;

/**
 * Profile Flair flair operators interface.
 */
interface flair_interface
{
	/**
	 * Get all flair items.
	 *
	 * @return array An array of flair entities
	 */
	public function get_flair();

	/**
	 * Add a flair item.
	 *
	 * @param \stevotvr\flair\entity\flair_interface	$flair
	 *
	 * @return \stevotvr\flair\entity\flair_interface The added flair entity
	 */
	public function add_flair($flair);

	/**
	 * Delete a flair item.
	 *
	 * @param int	$flair_id	The database ID of the flair item
	 *
	 * @return bool The record was deleted
	 */
	public function delete_flair($flair_id);
}
