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

use stevotvr\flair\exception\out_of_bounds;

/**
 * Profile Flair flair operator.
 */
class flair extends operator implements flair_interface
{
	public function get_flair($cat_id = -1)
	{
		$entities = array();

		$where = ($cat_id > -1) ? 'WHERE flair_category = ' . (int) $cat_id : '';
		$sql = 'SELECT *
				FROM ' . $this->flair_table . '
				' . $where . '
				ORDER BY flair_order ASC, flair_id ASC';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('stevotvr.flair.entity.flair')->import($row);
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
		$sql = 'DELETE FROM ' . $this->fav_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->group_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->notif_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->trigger_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->user_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . $this->flair_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	public function move_flair($flair_id, $offset)
	{
		$sql = 'SELECT flair_category
				FROM ' . $this->flair_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row === false)
		{
			throw new out_of_bounds('flair_id');
		}

		$sql = 'SELECT flair_id
				FROM ' . $this->flair_table . '
				WHERE flair_category = ' . (int) $row['flair_category'] . '
				ORDER BY flair_order ASC, flair_id ASC';
		$result = $this->db->sql_query($sql);

		$ids = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$ids[] = $row['flair_id'];
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
					WHERE flair_id = ' . (int) $id;
			$this->db->sql_query($sql);
		}
	}

	public function assign_groups($flair_id, array $group_ids)
	{
		$sql = 'DELETE FROM ' . $this->group_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);

		$sql_ary = array();
		foreach ($group_ids as $group_id)
		{
			$sql_ary[] = array(
				'group_id'	=> (int) $group_id,
				'flair_id'	=> (int) $flair_id,
			);
		}
		$this->db->sql_multi_insert($this->group_table, $sql_ary);
	}

	public function get_assigned_groups($flair_id)
	{
		$group_ids = array();

		$sql = 'SELECT group_id
				FROM ' . $this->group_table . '
				WHERE flair_id = ' . (int) $flair_id;
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$group_ids[] = (int) $row['group_id'];
		}
		$this->db->sql_freeresult($result);

		return $group_ids;
	}

	public function get_group_flair($group_ids = array())
	{
		$group_ids = (array) $group_ids;
		$flair = array();

		if (empty($group_ids))
		{
			return $flair;
		}

		$flair_ids = array();
		$sql = 'SELECT flair_id
				FROM ' . $this->group_table . '
				WHERE ' . $this->db->sql_in_set('group_id', $group_ids);
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$flair_ids[] = (int) $row['flair_id'];
		}
		$this->db->sql_freeresult();

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.*',
			'FROM'		=> array($this->flair_table => 'f'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->cat_table => 'c'),
					'ON'	=> 'c.cat_id = f.flair_category',
				),
			),
			'WHERE'		=> 'f.flair_groups_auto = 0 AND ' . $this->db->sql_in_set('f.flair_id', $flair_ids),
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_ary);
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$this->import_flair_item($flair, $row);
		}
		$this->db->sql_freeresult();

		$user_flair_ids = array();
		$sql = 'SELECT flair_id
				FROM ' . $this->user_table . '
				WHERE ' . $this->db->sql_in_set('flair_id', $flair_ids);
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$user_flair_ids[] = (int) $row['flair_id'];
		}
		$this->db->sql_freeresult();

		foreach ($flair as &$category)
		{
			foreach ($category['items'] as &$item)
			{
				$item['count'] = (int) in_array($item['flair']->get_id(), $user_flair_ids);
			}
		}

		self::sort_flair($flair);

		return $flair;
	}

	public function delete_group($group_id)
	{
		$sql = 'DELETE FROM ' . $this->group_table . '
				WHERE group_id = ' . (int) $group_id;
		$this->db->sql_query($sql);
	}
}
