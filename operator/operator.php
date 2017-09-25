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

use phpbb\db\driver\driver_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair operator base class.
 */
abstract class operator
{
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * The name of the flair table.
	 *
	 * @var string
	 */
	protected $flair_table;

	/**
	 * The name of the flair_categories table.
	 *
	 * @var string
	 */
	protected $cat_table;

	/**
	 * The name of the flair_groups table.
	 *
	 * @var string
	 */
	protected $group_table;

	/**
	 * The name of the flair_triggers table.
	 *
	 * @var string
	 */
	protected $trigger_table;

	/**
	 * The name of the flair_users table.
	 *
	 * @var string
	 */
	protected $user_table;

	/**
	 * @param ContainerInterface                $container
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string                            $flair_table   The name of the flair table
	 * @param string                            $cat_table     The name of the flair_categories table
	 * @param string                            $group_table   The name of the flair_groups table
	 * @param string                            $trigger_table The name of the flair_triggers table
	 * @param string                            $user_table    The name of the flair_users table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $flair_table, $cat_table, $group_table, $trigger_table, $user_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->flair_table = $flair_table;
		$this->cat_table = $cat_table;
		$this->group_table = $group_table;
		$this->trigger_table = $trigger_table;
		$this->user_table = $user_table;
	}
}
