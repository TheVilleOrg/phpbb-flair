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
 * Profile Flair flair trigger operators interface.
 */
interface trigger_interface
{
	/**
	 * Get all of the triggers of a specified name.
	 *
	 * @param string $trigger_name The trigger name
	 *
	 * @return array An array of trigger entities
	 */
	public function get_triggera($trigger_name);

	/**
	 * Get all the triggers for a specified flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 *
	 * @return array An array of trigger entities
	 */
	public function get_flair_triggers($flair_id);

	/**
	 * Set the triggers for a flair item.
	 *
	 * @param int   $flair_id      The database ID of the flair item
	 * @param array $trigger_names An associative array of trigger names to values
	 */
	public function set_triggers($flair_id, array $trigger_names);
}
