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
 * Profile Flair user operator.
 */
class user implements user_interface
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
	 * The name of the flair_users table.
	 *
	 * @var string
	 */
	protected $user_table;

	/**
	 * @param ContainerInterface                $container
	 * @param \phpbb\db\driver\driver_interface $db
	 * @param string                            $flair_table The name of the flair table
	 * @param string                            $user_table  The name of the flair_users table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $flair_table, $user_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->flair_table = $flair_table;
		$this->user_table = $user_table;
	}

	public function add_flair($user_id, $flair_id, $count = 1)
	{
		if ($count < 1)
		{
			return;
		}

		$old_count = $this->get_item_count($user_id, $flair_id);

		if ($old_count !== false)
		{
			$this->update_count($user_id, $flair_id, $old_count + $count);
			return;
		}

		$this->insert_row($user_id, $flair_id, $count);
	}

	public function remove_flair($user_id, $flair_id, $count = 1)
	{
		if ($count < 1)
		{
			return;
		}

		$old_count = $this->get_item_count($user_id, $flair_id);

		if ($old_count !== false)
		{
			if ($old_count - $count <= 0)
			{
				$this->delete_row($user_id, $flair_id);
				return;
			}

			$this->update_count($user_id, $flair_id, $old_count - $count);
		}
	}

	public function set_flair_count($user_id, $flair_id, $count)
	{
		if ($count < 1)
		{
			$this->delete_row($user_id, $flair_id);
			return;
		}

		$this->update_count($user_id, $flair_id, $count);

		if ($this->db->sql_affectedrows() === 0)
		{
			$this->insert_row($user_id, $flair_id, $count);
		}
	}

	public function get_user_flair($user_ids, $filter = '')
	{
		$where = $this->db->sql_in_set('u.user_id', (array) $user_ids);
		if (in_array($filter, array('profile', 'posts')))
		{
			$where .= ' AND (c.flair_display_' . $filter . ' <> 0 OR c.flair_id IS NULL)';
		}

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.flair_name AS cat_name, u.user_id, u.flair_count',
			'FROM'		=> array($this->user_table	=> 'u'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->flair_table	=> 'f'),
					'ON'	=> 'f.flair_id = u.flair_id',
				),
				array(
					'FROM'	=> array($this->flair_table	=> 'c'),
					'ON'	=> 'c.flair_id = f.flair_parent',
				),
			),
			'WHERE'		=> $where,
			'ORDER_BY'	=> 'c.flair_order ASC, c.flair_id ASC, f.flair_order ASC, f.flair_id ASC',
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);

		$result = $this->db->sql_query($sql);

		$user_flair = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_flair[(int) $row['user_id']][(int) $row['flair_parent']]['category'] = $row['cat_name'];
			$entity = $this->container->get('stevotvr.flair.entity')->import($row);
			$user_flair[(int) $row['user_id']][(int) $row['flair_parent']]['items'][] = array(
				'count'	=> (int) $row['flair_count'],
				'flair'	=> $entity,
			);
		}
		return $user_flair;
	}

	/**
	 * Get the number of a specified flair item associated with a user.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 *
	 * @return int|boolean The number of the specified item associated with the user. false if none
	 */
	protected function get_item_count($user_id, $flair_id)
	{
		$sql = 'SELECT flair_count
				FROM ' . $this->user_table . '
				WHERE user_id = ' . (int) $user_id . '
					AND flair_id = ' . (int) $flair_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row !== false)
		{
			return (int) $row['flair_count'];
		}

		return false;
	}

	/**
	 * Insert a row into the flair_users table.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 * @param int $count    The item count
	 */
	protected function insert_row($user_id, $flair_id, $count = 1)
	{
		$data = array(
			'user_id'		=> (int) $user_id,
			'flair_id'		=> (int) $flair_id,
			'flair_count'	=> (int) $count,
		);
		$sql = 'INSERT INTO ' . $this->user_table . '
				' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);
	}

	/**
	 * Delete a row from the flair_users table.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 */
	protected function delete_row($user_id, $flair_id)
	{
		$sql = 'DELETE FROM ' . $this->user_table . '
				WHERE user_id = ' . (int) $user_id . '
					AND flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Update the flair_count column of a row in the flair_users table.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 * @param int $count    The new item count
	 */
	protected function update_count($user_id, $flair_id, $count)
	{
		$sql = 'UPDATE ' . $this->user_table . '
				SET flair_count = ' . (int) $count . '
				WHERE user_id = ' . (int) $user_id . '
					AND flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);
	}
}
