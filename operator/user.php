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
class user extends subject implements user_interface
{
	public function get_user_flair(array $user_ids, $filter = '')
	{
		$flair = array();

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
			$user_id = (int) $row['user_id'];
			$flair_id = (int) $row['flair_id'];
			$flair_category = (int) $row['flair_category'];
			$flair_count = (int) $row['flair_count'];

			$entity = $this->container->get('stevotvr.flair.entity.category');
			if ($row['cat_id'])
			{
				$entity->import($row);
			}
			$flair[$user_id][$flair_category]['category'] = $entity;

			$entity = $this->container->get('stevotvr.flair.entity.flair')->import($row);
			$flair[$user_id][$flair_category]['items'][$flair_id] = array(
				'count'	=> $flair_count,
				'flair'	=> $entity,
			);
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

		$where = $this->db->sql_in_set('g.group_id', array_keys($memberships));
		if (in_array($filter, array('profile', 'posts')))
		{
			$where .= ' AND (c.cat_display_' . $filter . ' <> 0 OR c.cat_id IS NULL)';
		}

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.*, g.group_id, g.flair_count',
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
			$flair_count = (int) $row['flair_count'];

			foreach ($memberships[(int) $row['group_id']] as $user_id)
			{
				if (isset($flair[$user_id][$flair_category]['items'][$flair_id]))
				{
					$flair[$user_id][$flair_category]['items'][$flair_id]['count'] += $flair_count;
					continue;
				}

				$entity = $this->container->get('stevotvr.flair.entity.category');
				if ($row['cat_id'])
				{
					$entity->import($row);
				}
				$flair[$user_id][$flair_category]['category'] = $entity;

				$entity = $this->container->get('stevotvr.flair.entity.flair')->import($row);
				$flair[$user_id][$flair_category]['items'][$flair_id] = array(
					'count'	=> $row['flair_count'],
					'flair'	=> $entity,
				);
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
