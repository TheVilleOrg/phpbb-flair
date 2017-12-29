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
 * Profile Flair user operator.
 */
class user extends operator implements user_interface
{
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

	public function get_flair($user_id)
	{
		$flair = array();

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.*, u.flair_count',
			'FROM'		=> array($this->user_table => 'u'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->flair_table	=> 'f'),
					'ON'	=> 'f.flair_id = u.flair_id',
				),
				array(
					'FROM'	=> array($this->cat_table	=> 'c'),
					'ON'	=> 'c.cat_id = f.flair_category',
				),
			),
			'WHERE'		=> 'u.user_id = ' . (int) $user_id,
			'ORDER_BY'	=> 'c.cat_order ASC, c.cat_id ASC, f.flair_order ASC, f.flair_id ASC',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$this->import_flair_item($flair, $row);
		}
		$this->db->sql_freeresult($result);

		return $flair;
	}

	public function get_user_flair(array $user_ids, $filter = '')
	{
		$flair = array();
		if (empty($user_ids))
		{
			return $flair;
		}

		$where = $this->db->sql_in_set('u.user_id', $user_ids);
		if (in_array($filter, array('profile', 'posts')))
		{
			$where .= ' AND (c.cat_display_' . $filter . ' <> 0 OR c.cat_id IS NULL)';
		}

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.*, u.user_id, u.flair_count',
			'FROM'		=> array($this->user_table => 'u'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->flair_table => 'f'),
					'ON'	=> 'f.flair_id = u.flair_id',
				),
				array(
					'FROM'	=> array($this->cat_table => 'c'),
					'ON'	=> 'c.cat_id = f.flair_category',
				),
			),
			'WHERE'		=> $where,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if (!isset($flair[(int) $row['user_id']]))
			{
				$flair[(int) $row['user_id']] = array();
			}
			$this->import_flair_item($flair[(int) $row['user_id']], $row);
		}
		$this->db->sql_freeresult($result);

		$this->get_group_flair($user_ids, $filter, $flair);
		self::sort_flair($flair);

		return $flair;
	}

	/**
	 * Load the group flair items for a list of users.
	 *
	 * @param array  $user_ids An array of user database IDs
	 * @param string $filter   Set to profile or posts to only get items shown in that area
	 * @param array  &$flair   The user flair to which to add group flair
	 */
	protected function get_group_flair(array $user_ids, $filter, array &$flair)
	{
		$memberships = $this->get_group_memberships($user_ids);
		if (empty($memberships))
		{
			return;
		}

		$where = $this->db->sql_in_set('g.group_id', array_keys($memberships));
		if (in_array($filter, array('profile', 'posts')))
		{
			$where .= ' AND (c.cat_display_' . $filter . ' <> 0 OR c.cat_id IS NULL)';
		}

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.*, g.group_id',
			'FROM'		=> array($this->group_table => 'g'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->flair_table => 'f'),
					'ON'	=> 'f.flair_id = g.flair_id',
				),
				array(
					'FROM'	=> array($this->cat_table => 'c'),
					'ON'	=> 'c.cat_id = f.flair_category',
				),
			),
			'WHERE'		=> $where,
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$flair_id = (int) $row['flair_id'];
			$flair_category = (int) $row['flair_category'];

			foreach ($memberships[(int) $row['group_id']] as $user_id)
			{
				if (isset($flair[$user_id][$flair_category]['items'][$flair_id]))
				{
					continue;
				}

				if (!isset($flair[$user_id]))
				{
					$flair[$user_id] = array();
				}
				$this->import_flair_item($flair[$user_id], $row);
			}
		}
		$this->db->sql_freeresult($result);
	}

	/**
	 * Get the map of group IDs to user IDs for a list of users.
	 *
	 * @param array $user_ids An array of user database IDs
	 *
	 * @return array Array mapping group IDs to arrays of user IDs
	 */
	protected function get_group_memberships(array $user_ids)
	{
		$memberships = array();

		$sql = 'SELECT user_id, group_id
				FROM ' . USER_GROUP_TABLE . '
				WHERE ' . $this->db->sql_in_set('user_id', $user_ids);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$memberships[(int) $row['group_id']][] = (int) $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		return $memberships;
	}

	/**
	 * Get the number of a specified flair item associated with a user.
	 *
	 * @param int $user_id  The database ID of the user
	 * @param int $flair_id The database ID of the flair item
	 *
	 * @return int|boolean The number of the specified item associated with the user. false if
	 *                         there are none
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
	 * Insert a row into the table.
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
	 * Delete a row from the table.
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
	 * Update the flair_count column of a row in the table.
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

	/**
	 * Import a flair item from a database query result row.
	 *
	 * @param array &$flair The array to which to add the item
	 * @param array $row    The database result row data
	 */
	protected function import_flair_item(array &$flair, array $row)
	{
		$entity = $this->container->get('stevotvr.flair.entity.category');
		if ($row['cat_id'])
		{
			$entity->import($row);
		}
		$flair[(int) $row['flair_category']]['category'] = $entity;

		$entity = $this->container->get('stevotvr.flair.entity.flair')->import($row);
		$item = array(
			'count'	=> isset($row['flair_count']) ? (int) $row['flair_count'] : 1,
			'flair'	=> $entity,
		);
		$flair[(int) $row['flair_category']]['items'][(int) $row['flair_id']] = $item;
	}

	/**
	 * Sort a user flair array.
	 *
	 * @param array &$flair The flair array to sort
	 */
	static protected function sort_flair(array &$flair)
	{
		foreach ($flair as &$user_flair)
		{
			usort($user_flair, array('self', 'cmp_cats'));
			foreach ($user_flair as &$category)
			{
				usort($category['items'], array('self', 'cmp_items'));
			}
		}
	}

	/**
	 * Comparison function for sorting flair category arrays.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	static protected function cmp_cats($a, $b)
	{
		return $a['category']->get_order() - $b['category']->get_order();
	}

	/**
	 * Comparison function for sorting flair item arrays.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	static protected function cmp_items($a, $b)
	{
		return $a['flair']->get_order() - $b['flair']->get_order();
	}
}
