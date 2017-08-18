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
 * Profile Flair flair entity.
 */
class flair extends entity implements flair_interface
{
	protected $columns = array(
		'flair_id'			=> 'integer',
		'flair_cat_id'		=> 'integer',
		'flair_name'		=> 'set_name',
		'flair_desc'		=> 'set_desc',
		'flair_order'		=> 'set_order',
		'flair_color'		=> 'set_color',
		'flair_icon_file'	=> 'set_icon',
		'flair_icon_width'	=> 'integer',
		'flair_icon_height'	=> 'integer',
	);

	protected $id_column = 'flair_id';

	public function get_cat_id()
	{
		return isset($this->data['flair_cat_id']) ? (int) $this->data['flair_cat_id'] : 0;
	}

	public function set_cat_id($cat_id)
	{
		$cat_id = (int) $cat_id;

		if ($cat_id < 0)
		{
			throw new out_of_bounds('flair_cat_id');
		}

		$this->data['flair_cat_id'] = $cat_id;

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

	public function get_icon_file()
	{
		return isset($this->data['flair_icon_file']) ? (string) $this->data['flair_icon_file'] : '';
	}

	public function get_icon_width()
	{
		return isset($this->data['flair_icon_width']) ? (int) $this->data['flair_icon_width'] : 0;
	}

	public function get_icon_height()
	{
		return isset($this->data['flair_icon_height']) ? (int) $this->data['flair_icon_height'] : 0;
	}

	public function set_icon($file, $width = 0, $height = 0)
	{
		$file = (string) $file;
		$width = (int) $width;
		$height = (int) $height;

		if (truncate_string($file, 50) !== $file)
		{
			throw new unexpected_value(array('flair_icon_file', 'TOO_LONG'));
		}

		if ($width < 0 || $width > 16777215)
		{
			throw new out_of_bounds('flair_icon_width');
		}

		if ($height < 0 || $height > 16777215)
		{
			throw new out_of_bounds('flair_icon_height');
		}

		$this->data['flair_icon_file'] = $file;
		$this->data['flair_icon_width'] = $width;
		$this->data['flair_icon_height'] = $height;

		return $this;
	}
}
