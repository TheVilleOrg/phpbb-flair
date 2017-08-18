<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\entity;

/**
 * Profile Flair category entity interface.
 */
interface category_interface extends entity_interface
{
	/**
	 * @return string The name of the category
	 */
	public function get_name();

	/**
	 * @param string $name The name of the category
	 *
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_name($name);

	/**
	 * @return int The order of the category
	 */
	public function get_order();

	/**
	 * @param int $order The order of the category
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_order($order);
}
