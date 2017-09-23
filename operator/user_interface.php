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
 * Profile Flair user operators interface.
 */
interface user_interface
{
	/**
	 * Add a flair item to a user. This will add the item or increment the count if it already
	 * exists.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 * @param int $count    The number by which to increment
	 */
	public function add_flair($user_id, $flair_id, $count = 1);

	/**
	 * Remove a flair item from a user. This will either decrement the count or delete the item
	 * if the count is 1.
	 *
	 * @param int $user_id   The database ID of the user
	 * @param int $flair_id  The database ID of the flair item
	 * @param int $count     The number by which to decrement
	 */
	public function remove_flair($user_id, $flair_id, $count = 1);

	/**
	 * Set the count on a flair item for a user.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 * @param int $count    The count to set
	 */
	public function set_flair_count($user_id, $flair_id, $count);

	/**
	 * Get the flair for a user.
	 *
	 * @param int $user_id The database ID of the user
	 *
	 * @return array An associative array of arrays of flair rows
	 *                  flair_parent
	 *                     count int
	 *                     flair \stevotvr\flair\entity\flair
	 */
	public function get_flair($user_id);

	/**
	 * Get the flair for a list of users.
	 *
	 * @param array  $user_ids An array of user database IDs
	 * @param string $filter   Set to profile or posts to only get items shown in that area
	 *
	 * @return array An associative array of associative arrays of arrays of flair rows
	 *                  user_id
	 *                     flair_parent
	 *                        count int
	 *                        flair \stevotvr\flair\entity\flair
	 */
	public function get_user_flair(array $user_ids, $filter = '');
}
