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

use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;

/**
 * Profile Flair category entity interface.
 */
class category extends entity implements category_interface
{
	protected $columns = array(
		'flair_cat_id'		=> 'integer',
		'flair_cat_name'	=> 'set_name',
		'flair_cat_order'	=> 'set_order',
	);

	protected $id_column = 'flair_cat_id';

	public function get_name()
	{
		return isset($this->data['flair_cat_name']) ? (string) $this->data['flair_cat_name'] : '';
	}

	public function set_name($name)
	{
		$name = (string) $name;

		if ($name === '')
		{
			throw new unexpected_value(array('flair_cat_name', 'FIELD_MISSING'));
		}

		if (truncate_string($name, 50) !== $name)
		{
			throw new unexpected_value(array('flair_cat_name', 'TOO_LONG'));
		}

		$this->data['flair_cat_name'] = $name;

		return $this;
	}

	public function get_order()
	{
		return isset($this->data['flair_cat_order']) ? (int) $this->data['flair_cat_order'] : 0;
	}

	public function set_order($order)
	{
		$order = (int) $order;

		if ($order < 0 || $order > 16777215)
		{
			throw new out_of_bounds('flair_cat_order');
		}

		$this->data['flair_cat_order'] = $order;

		return $this;
	}
}
