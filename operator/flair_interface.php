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
	 * @param int $cat_id The database ID of the category for which to get items, -1 for all
	 *
	 * @return array An array of flair entities
	 */
	public function get_flair($cat_id = -1);

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
	 * Set the list of groups whose members will automatically be assigned a flair item.
	 *
	 * @param int   $flair_id  The database ID of the flair item
	 * @param array $group_ids The list of group IDs to which to assign this item
	 */
	public function assign_groups($flair_id, array $group_ids);

	/**
	 * Get the list of groups which is assigned this flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 *
	 * @return array The list of group database IDs
	 */
	public function get_assigned_groups($flair_id);
}
