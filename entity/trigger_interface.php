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
 * Profile Flair trigger entity interface.
 */
interface trigger_interface extends entity_interface
{
	/**
	 * @return int The flair item ID for this trigger
	 */
	public function get_flair();

	/**
	 * @param int $flair_id The flair item ID for this trigger
	 *
	 * @return trigger_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_flair($flair_id);

	/**
	 * @return string The name of this trigger
	 */
	public function get_name();

	/**
	 * @param string $name The name of this trigger
	 *
	 * @return trigger_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\missing_field
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_name($name);

	/**
	 * @return int The value for this trigger
	 */
	public function get_value();

	/**
	 * @param int $value The value for this trigger
	 *
	 * @return trigger_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_value($value);
}
