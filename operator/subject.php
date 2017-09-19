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
 * Profile Flair user/group operator base class.
 */
abstract class subject extends operator implements subject_interface
{
	/**
	 * The name of the unique ID column.
	 *
	 * @var string
	 */
	protected $id_column;

	/**
	 * @return string The name of the databases table
	 */
	abstract protected function get_table();

	public function add_flair($subject_id, $flair_id, $count = 1)
	{
		if ($count < 1)
		{
			return;
		}

		$old_count = $this->get_item_count($subject_id, $flair_id);

		if ($old_count !== false)
		{
			$this->update_count($subject_id, $flair_id, $old_count + $count);
			return;
		}

		$this->insert_row($subject_id, $flair_id, $count);
	}

	public function remove_flair($subject_id, $flair_id, $count = 1)
	{
		if ($count < 1)
		{
			return;
		}

		$old_count = $this->get_item_count($subject_id, $flair_id);

		if ($old_count !== false)
		{
			if ($old_count - $count <= 0)
			{
				$this->delete_row($subject_id, $flair_id);
				return;
			}

			$this->update_count($subject_id, $flair_id, $old_count - $count);
		}
	}

	public function set_flair_count($subject_id, $flair_id, $count)
	{
		if ($count < 1)
		{
			$this->delete_row($subject_id, $flair_id);
			return;
		}

		$this->update_count($subject_id, $flair_id, $count);

		if ($this->db->sql_affectedrows() === 0)
		{
			$this->insert_row($subject_id, $flair_id, $count);
		}
	}

	/**
	 * Get the number of a specified flair item associated with a subject.
	 *
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 *
	 * @return int|boolean The number of the specified item associated with the subject. false if
	 *                         there are none
	 */
	protected function get_item_count($subject_id, $flair_id)
	{
		$sql = 'SELECT flair_count
				FROM ' . $this->get_table() . '
				WHERE ' . $this->id_column . ' = ' . (int) $subject_id . '
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
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 * @param int $count      The item count
	 */
	protected function insert_row($subject_id, $flair_id, $count = 1)
	{
		$data = array(
			$this->id_column	=> (int) $subject_id,
			'flair_id'			=> (int) $flair_id,
			'flair_count'		=> (int) $count,
		);
		$sql = 'INSERT INTO ' . $this->get_table() . '
				' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);
	}

	/**
	 * Delete a row from the table.
	 *
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 */
	protected function delete_row($subject_id, $flair_id)
	{
		$sql = 'DELETE FROM ' . $this->get_table() . '
				WHERE ' . $this->id_column . ' = ' . (int) $subject_id . '
					AND flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Update the flair_count column of a row in the table.
	 *
	 * @param int $subject_id The database ID of the subject
	 * @param int $flair_id   The database ID of the flair item
	 * @param int $count      The new item count
	 */
	protected function update_count($subject_id, $flair_id, $count)
	{
		$sql = 'UPDATE ' . $this->get_table() . '
				SET flair_count = ' . (int) $count . '
				WHERE ' . $this->id_column . ' = ' . (int) $subject_id . '
					AND flair_id = ' . (int) $flair_id;
		$this->db->sql_query($sql);
	}
}
