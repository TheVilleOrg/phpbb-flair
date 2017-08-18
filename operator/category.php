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
 * Profile Flair category operator.
 */
class category implements category_interface
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
	 * @var string The name of the categories table
	 */
	protected $cat_table;

	/**
	 * @var string The name of the flair table
	 */
	protected $flair_table;

	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface	$container
	 * @param \phpbb\db\driver\driver_interface							$db
	 * @param string													$cat_table		The name of the categories table
	 * @param string													$flair_table	The name of the flair table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $cat_table, $flair_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->cat_table = $cat_table;
		$this->flair_table = $flair_table;
	}

	public function get_categories()
	{
		$entities = array();

		$sql = 'SELECT *
				FROM ' . $this->cat_table . '
				ORDER BY flair_cat_order ASC, flair_cat_id ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('stevotvr.flair.entity.category')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	public function add_category($category)
	{
		$category->insert();
		$cat_id = $category->get_id();
		return $category->load($cat_id);
	}

	public function delete_category($cat_id)
	{
		$this->unlink_flair($cat_id);

		$sql = 'DELETE FROM ' . $this->cat_table . '
				WHERE flair_cat_id = ' . (int) $cat_id;
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	public function delete_flair($cat_id)
	{
		$sql = 'DELETE FROM ' . $this->flair_table . '
				WHERE flair_cat_id = ' . (int) $cat_id;
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
				SET flair_cat_id = 0
				WHERE flair_cat_id = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}
}
