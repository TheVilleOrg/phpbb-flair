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
 * Profile Flair category operators interface.
 */
interface category_interface
{
	/**
	 * Get all categories.
	 *
	 * @return array An array of category entities
	 */
	public function get_categories();

	/**
	 * Add a category.
	 *
	 * @param \stevotvr\flair\entity\category_interface	$category
	 *
	 * @return \stevotvr\flair\entity\category_interface The added category entity
	 */
	public function add_category($category);

	/**
	 * Delete a category.
	 *
	 * @param int	$cat_id	The database ID of the category
	 *
	 * @return bool The record was deleted
	 */
	public function delete_category($cat_id);

	/**
	 * Move a category up in the sorting order.
	 *
	 * @param int	$cat_id	The database ID of the category
	 */
	public function move_category_up($cat_id);

	/**
	 * Move a category down in the sorting order.
	 *
	 * @param int	$cat_id	The database ID of the category
	 */
	public function move_category_down($cat_id);

	/**
	 * Delete all flair items from a category.
	 *
	 * @param int	$cat_id	The database ID of the category
	 */
	public function delete_flair($cat_id);

	/**
	 * Reassign all flair items of a category to another category.
	 *
	 * @param int	$cat_id		The database ID of the category
	 * @param int	$new_cat_id	The database ID of the new category
	 */
	public function reassign_flair($cat_id, $new_cat_id);
}
