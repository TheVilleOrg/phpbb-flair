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

use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\db\driver\driver_interface;

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
	 * @param ContainerInterface                $container
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string                            $flair_table The name of the flair table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $flair_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->flair_table = $flair_table;
	}
}
