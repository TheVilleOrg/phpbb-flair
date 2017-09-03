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
	 * Get flair items.
	 *
	 * @param int     $parent_id The database ID of the parent for which to get items, -1 for all
	 * @param boolean $no_cats   Filter out categories
	 * @param boolean $no_flair  Filter out flair items
	 *
	 * @return array An array of flair entities
	 */
	public function get_flair($parent_id = -1, $no_cats = true, $no_flair = false);

	/**
	 * Add a flair item.
	 *
	 * @param \stevotvr\flair\entity\flair_interface $flair
	 *
	 * @return \stevotvr\flair\entity\flair_interface The added flair entity
	 */
	public function add_flair($flair);

	/**
	 * Delete a flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 *
	 * @return boolean The record was deleted
	 */
	public function delete_flair($flair_id);

	/**
	 * Move a flair item in the sorting order.
	 *
	 * @param int $flair_id The database ID of the flair item
	 * @param int $offset   The offset by which to move the flair item
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function move_flair($flair_id, $offset);

	/**
	 * Delete all flair items from a category.
	 *
	 * @param int $cat_id The database ID of the category
	 */
	public function delete_all_flair($cat_id);

	/**
	 * Reassign all flair items of a category to another category.
	 *
	 * @param int $cat_id     The database ID of the category
	 * @param int $new_cat_id The database ID of the new category
	 */
	public function reassign_flair($cat_id, $new_cat_id);
}
