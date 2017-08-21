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
use stevotvr\flair\exception\out_of_bounds;

/**
 * Profile Flair flair operator.
 */
class flair implements flair_interface
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
	 * @var string The name of the flair table
	 */
	protected $flair_table;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface	$container
	 * @param \phpbb\db\driver\driver_interface							$db
	 * @param string													$flair_table	The name of the flair table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $flair_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->flair_table = $flair_table;
	}

	public function get_flair($parent_id = -1, $get_cats = false)
	{
		$entities = array();

		$where = '1 = 1';
		if ($get_cats)
		{
			$where = 'flair_is_cat <> 0';
		}
		elseif ($parent_id > -1)
		{
			$where = 'flair_is_cat = 0 AND flair_parent = ' . (int) $parent_id;
		}

		$sql = 'SELECT *
				FROM ' . $this->flair_table . '
				WHERE ' . $where . '
				ORDER BY flair_order ASC, flair_id ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('stevotvr.flair.entity')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	public function add_flair($flair)
	{
		$flair->insert();
		$flair_id = $flair->get_id();
		return $flair->load($flair_id);
	}

	public function delete_flair($flair_id)
	{
		$this->unlink_flair($flair_id);

		$sql = 'DELETE FROM ' . $this->flair_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	public function move_flair($flair_id, $offset)
	{
		$sql = 'SELECT flair_parent, flair_is_cat
				FROM ' . $this->flair_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row === false)
		{
			throw new out_of_bounds('flair_id');
		}

		$where = $row['flair_is_cat'] ? 'flair_is_cat <> 0' : 'flair_is_cat = 0 AND flair_parent = ' . $row['flair_parent'];

		$sql = 'SELECT flair_id
				FROM ' . $this->flair_table . '
				WHERE ' . $where . '
				ORDER BY flair_order ASC, flair_id ASC';
		$result = $this->db->sql_query($sql);

		$ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ids[] = (int) $row['flair_id'];
		}
		$this->db->sql_freeresult($result);

		$position = array_search($flair_id, $ids);
		array_splice($ids, $position, 1);
		$position += $offset;
		array_splice($ids, $position, 0, $flair_id);

		foreach ($ids as $pos => $id)
		{
			$sql = 'UPDATE ' . $this->flair_table . '
					SET flair_order = ' . $pos . '
					WHERE flair_id = ' . $id;
			$this->db->sql_query($sql);
		}
	}

	public function delete_all_flair($cat_id)
	{
		$sql = 'DELETE FROM ' . $this->flair_table . '
				WHERE flair_parent = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}

	public function reassign_flair($cat_id, $new_cat_id)
	{
		$sql = 'UPDATE ' . $this->flair_table . '
				SET flair_parent = ' . (int) $new_cat_id . '
				WHERE flair_parent = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Unlink all flair items from a category.
	 *
	 * @param int	$cat_id	The database ID of the category
	 */
	protected function unlink_flair($cat_id)
	{
		$sql = 'UPDATE ' . $this->flair_table . '
				SET flair_parent = 0
				WHERE flair_parent = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}
}
