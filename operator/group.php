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
 * Profile Flair group operator.
 */
class group extends subject implements group_interface
{
	protected $id_column = 'group_id';

	protected function get_table()
	{
		return $this->group_table;
	}

	public function get_flair($subject_id)
	{
		$sql_ary = array(
			'SELECT'	=> 'f.*, c.cat_name, g.flair_count',
			'FROM'		=> array($this->group_table	=> 'g'),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array($this->flair_table	=> 'f'),
					'ON'	=> 'f.flair_id = g.flair_id',
				),
				array(
					'FROM'	=> array($this->cat_table	=> 'c'),
					'ON'	=> 'c.cat_id = f.flair_category',
				),
			),
			'WHERE'		=> 'g.group_id = ' . (int) $subject_id,
			'ORDER_BY'	=> 'c.cat_order ASC, c.cat_id ASC, f.flair_order ASC, f.flair_id ASC',
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_ary);

		$result = $this->db->sql_query($sql);

		$group_flair = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$group_flair[(int) $row['flair_category']]['category'] = $row['cat_name'];
			$entity = $this->container->get('stevotvr.flair.entity.flair')->import($row);
			$group_flair[(int) $row['flair_category']]['items'][] = array(
				'count'	=> (int) $row['flair_count'],
				'flair'	=> $entity,
			);
		}
		return $group_flair;
	}
}
