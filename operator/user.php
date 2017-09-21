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
	public function get_flair($subject_id)
	{
		$user_flair = $this->get_user_flair($subject_id);
		return count($user_flair) ? $user_flair[(int) $subject_id] : array();
	}

	public function get_user_flair($user_ids, $filter = '')
	{
		$where = $this->db->sql_in_set('u.user_id', (array) $user_ids);
		if (in_array($filter, array('profile', 'posts')))
		{
			$where .= ' AND (c.cat_display_' . $filter . ' <> 0 OR c.cat_id IS NULL)';
		}

		$sql_ary = array(
			'SELECT'	=> 'f.*, c.cat_name, u.user_id, u.flair_count',
			'FROM'		=> array($this->user_table	=> 'u'),
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
			'WHERE'		=> $where,
			'ORDER_BY'	=> 'c.cat_order ASC, c.cat_id ASC, f.flair_order ASC, f.flair_id ASC',
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);

		$result = $this->db->sql_query($sql);

		$user_flair = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_flair[(int) $row['user_id']][(int) $row['flair_category']]['category'] = $row['cat_name'];
			$entity = $this->container->get('stevotvr.flair.entity.flair')->import($row);
			$user_flair[(int) $row['user_id']][(int) $row['flair_category']]['items'][] = array(
				'count'	=> (int) $row['flair_count'],
				'flair'	=> $entity,
			);
		}
		return $user_flair;
	}
}
