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
 * Profile Flair user/group operators interface.
 */
interface subject_interface
{
	/**
	 * Add a flair item to a subject. This will add the item or increment the count if it already
	 * exists.
	 *
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 * @param int $count      The number by which to increment
	 */
	public function add_flair($subject_id, $flair_id, $count = 1);

	/**
	 * Remove a flair item from a subject. This will either decrement the count or delete the item
	 * if the count is 1.
	 *
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 * @param int $count      The number by which to decrement
	 */
	public function remove_flair($subject_id, $flair_id, $count = 1);

	/**
	 * Set the count on a flair item for a subject.
	 *
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 * @param int $count      The count to set
	 */
	public function set_flair_count($subject_id, $flair_id, $count);

	/**
	 * Get the flair for a subject.
	 *
	 * @param int $subject_id The database ID of the subject
	 *
	 * @return array An associative array of arrays of flair rows
	 *                  flair_parent
	 *                     count int
	 *                     flair \stevotvr\flair\entity\flair
	 */
	public function get_flair($subject_id);
}
