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

use stevotvr\flair\exception\missing_field;
use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;

/**
 * Profile Flair flair trigger entity.
 */
class trigger extends entity implements trigger_interface
{
	protected $columns = array(
		'trig_id'		=> 'integer',
		'flair_id'		=> 'set_flair',
		'trig_name'		=> 'set_name',
		'trig_value'	=> 'set_value',
	);

	protected $id_column = 'trig_id';

	public function get_flair()
	{
		return isset($this->data['flair_id']) ? (int) $this->data['flair_id'] : 0;
	}

	public function set_flair($flair_id)
	{
		$flair_id = (int) $flair_id;

		if ($flair_id < 0)
		{
			throw new out_of_bounds('flair_id');
		}

		$this->data['flair_id'] = $flair_id;

		return $this;
	}

	public function get_name()
	{
		return isset($this->data['trig_name']) ? (string) $this->data['trig_name'] : '';
	}

	public function set_name($name)
	{
		$name = (string) $name;

		if ($name === '')
		{
			throw new missing_field('trig_name');
		}

		if (truncate_string($name, 255) !== $name)
		{
			throw new unexpected_value('trig_name', 'TOO_LONG');
		}

		$this->data['trig_name'] = $name;

		return $this;
	}

	public function get_value()
	{
		return isset($this->data['trig_value']) ? (int) $this->data['trig_value'] : 0;
	}

	public function set_value($value)
	{
		$value = (int) $value;

		if ($value < 0 || $value > 16777215)
		{
			throw new out_of_bounds('trig_value');
		}

		$this->data['trig_value'] = $value;

		return $this;
	}
}
