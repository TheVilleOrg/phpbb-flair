<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\entity;

use phpbb\db\driver\driver_interface;
use stevotvr\flair\exception\invalid_argument;
use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;

/**
 * Profile Flair flair entity.
 */
class flair implements flair_interface
{
	/**
	 * @var \phpbb\db\driver\driver_interface
	 */
	protected $db;

	/**
	 * @var array The data for this entity
	 *      	flair_id
	 *      	flair_is_cat
	 *      	flair_parent
	 *      	flair_name
	 *      	flair_desc
	 *      	flair_order
	 *      	flair_color
	 *      	flair_icon
	 *      	flair_icon_color
	 */
	protected $data = array();

	/**
	 * @var string The name of the database table
	 */
	protected $table_name;

	/**
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param string							$table_name	The name of the database table
	 */
	public function __construct(driver_interface $db, $table_name)
	{
		$this->db = $db;
		$this->table_name = $table_name;
	}

	public function load($id)
	{
		$sql = 'SELECT *
				FROM ' . $this->table_name . '
				WHERE flair_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->data === false)
		{
			throw new out_of_bounds('flair_id');
		}

		return $this;
	}

	public function import(array $data)
	{
		$this->data = array();

		$columns = array(
			'flair_id'			=> 'integer',
			'flair_is_cat'		=> 'integer',
			'flair_parent'		=> 'integer',
			'flair_name'		=> 'set_name',
			'flair_desc'		=> 'set_desc',
			'flair_order'		=> 'set_order',
			'flair_color'		=> 'set_color',
			'flair_icon'		=> 'set_icon',
			'flair_icon_color'	=> 'set_icon_color',
		);

		foreach ($columns as $column => $type)
		{
			if (!isset($data[$column]))
			{
				throw new invalid_argument(array($column, 'FIELD_MISSING'));
			}

			if (method_exists($this, $type))
			{
				$this->$type($data[$column]);
				continue;
			}

			if ($type === 'integer' && $data[$column] < 0)
			{
				throw new out_of_bounds($column);
			}

			$value = $data[$column];
			settype($value, $type);
			$this->data[$column] = $value;
		}

		return $this;
	}

	public function insert()
	{
		if (!empty($this->data['flair_id']))
		{
			throw new out_of_bounds('flair_id');
		}

		$sql = 'INSERT INTO ' . $this->table_name . '
				' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		$this->data['flair_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	public function save()
	{
		if (empty($this->data['flair_id']))
		{
			throw new out_of_bounds('flair_id');
		}

		$data = array_diff_key($this->data, array('flair_id' => null));
		$sql = 'UPDATE ' . $this->table_name . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE flair_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	public function get_id()
	{
		return isset($this->data['flair_id']) ? (int) $this->data['flair_id'] : 0;
	}

	public function is_category()
	{
		return (bool) $this->data['flair_is_cat'];
	}

	public function set_category($is_category)
	{
		$is_category = (bool) $is_category;

		$this->data['flair_is_cat'] = (int) $is_category;

		return $this;
	}

	public function get_parent()
	{
		return isset($this->data['flair_parent']) ? (int) $this->data['flair_parent'] : 0;
	}

	public function set_parent($parent_id)
	{
		$parent_id = (int) $parent_id;

		if ($parent_id < 0)
		{
			throw new out_of_bounds('flair_parent');
		}

		$this->data['flair_parent'] = $parent_id;

		return $this;
	}

	public function get_name()
	{
		return isset($this->data['flair_name']) ? (string) $this->data['flair_name'] : '';
	}

	public function set_name($name)
	{
		$name = (string) $name;

		if ($name === '')
		{
			throw new unexpected_value(array('flair_name', 'FIELD_MISSING'));
		}

		if (truncate_string($name, 255) !== $name)
		{
			throw new unexpected_value(array('flair_name', 'TOO_LONG'));
		}

		$this->data['flair_name'] = $name;

		return $this;
	}

	public function get_desc()
	{
		return isset($this->data['flair_desc']) ? (string) $this->data['flair_desc'] : '';
	}

	public function set_desc($desc)
	{
		$this->data['flair_desc'] = (string) $desc;

		return $this;
	}

	public function get_order()
	{
		return isset($this->data['flair_order']) ? (int) $this->data['flair_order'] : 0;
	}

	public function set_order($order)
	{
		$order = (int) $order;

		if ($order < 0 || $order > 16777215)
		{
			throw new out_of_bounds('flair_order');
		}

		$this->data['flair_order'] = $order;

		return $this;
	}

	public function get_color()
	{
		return isset($this->data['flair_color']) ? (string) $this->data['flair_color'] : '';
	}

	public function set_color($color)
	{
		$color = (string) $color;

		if ($color !== '' && strlen($color) !== 6)
		{
			throw new out_of_bounds('flair_color');
		}

		$this->data['flair_color'] = $color;

		return $this;
	}

	public function get_icon()
	{
		return isset($this->data['flair_icon']) ? (string) $this->data['flair_icon'] : '';
	}

	public function set_icon($icon)
	{
		$icon = (string) $icon;

		if (truncate_string($icon, 50) !== $icon)
		{
			throw new unexpected_value(array('flair_icon', 'TOO_LONG'));
		}

		$this->data['flair_icon'] = $icon;

		return $this;
	}

	public function get_icon_color()
	{
		return isset($this->data['flair_icon_color']) ? (string) $this->data['flair_icon_color'] : '';
	}

	public function set_icon_color($color)
	{
		$color = (string) $color;

		if ($color !== '' && strlen($color) !== 6)
		{
			throw new out_of_bounds('flair_icon_color');
		}

		$this->data['flair_icon_color'] = $color;

		return $this;
	}
}
