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
interface user_interface extends subject_interface
{
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
