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
 * Profile Flair flair trigger operator.
 */
class trigger extends operator implements trigger_interface
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
	 * The name of the flair_triggers table.
	 *
	 * @var string
	 */
	protected $trigger_table;

	/**
	 * @param ContainerInterface                $container
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string                            $trigger_table The name of the flair_triggers table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $trigger_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->trigger_table = $trigger_table;
	}

	public function get_triggera($trigger_name)
	{
		return $this->get_trigger_rows("trig_name = '" . $this->db->sql_escape($trigger_name) . "'");
	}

	public function get_flair_triggers($flair_id)
	{
		return $this->get_trigger_rows('flair_id = ' . (int) $flair_id);
	}

	/**
	 * Get a list of trigger entities from the database.
	 *
	 * @param string $where The WHERE clause of the database query
	 *
	 * @return array An array of trigger entities
	 */
	protected function get_trigger_rows($where)
	{
		$entities = array();

		$sql = 'SELECT *
				FROM ' . $this->trigger_table . '
				WHERE ' . $where;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('stevotvr.flair.entity.trigger')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	public function set_triggers($flair_id, array $triggers)
	{
		$sql = 'DELETE FROM ' . $this->trigger_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql_ary = array();
		foreach ($triggers as $name => $value)
		{
			$sql_ary[] = array(
				'flair_id'		=> (int) $flair_id,
				'trig_name'		=> $name,
				'trig_value'	=> (int) $value,
			);
		}
		$this->db->sql_multi_insert($this->trigger_table, $sql_ary);
	}
}
