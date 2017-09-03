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

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;

/**
 * Profile Flair flair entity.
 */
class flair extends entity implements flair_interface
{
	/**
	 * @var \phpbb\config\config
	 */
	protected $config;

	protected $columns = array(
		'flair_id'						=> 'integer',
		'flair_is_cat'					=> 'set_category',
		'flair_parent'					=> 'integer',
		'flair_name'					=> 'set_name',
		'flair_desc'					=> 'string',
		'flair_desc_bbcode_uid'			=> 'string',
		'flair_desc_bbcode_bitfield'	=> 'string',
		'flair_desc_bbcode_options'		=> 'integer',
		'flair_order'					=> 'set_order',
		'flair_color'					=> 'set_color',
		'flair_icon'					=> 'set_icon',
		'flair_icon_color'				=> 'set_icon_color',
		'flair_font_color'				=> 'set_font_color',
		'flair_display_profile'			=> 'set_show_on_profile',
		'flair_display_posts'			=> 'set_show_on_posts',
	);

	protected $id_column = 'flair_id';

	/**
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param string							$table_name	The name of the database table
	 */
	public function __construct(config $config, driver_interface $db, $table_name)
	{
		parent::__construct($db, $table_name);
		$this->config = $config;
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

	public function get_desc_for_edit()
	{
		$content = isset($this->data['flair_desc']) ? $this->data['flair_desc'] : '';
		$uid = isset($this->data['flair_desc_bbcode_uid']) ? $this->data['flair_desc_bbcode_uid'] : '';
		$options = isset($this->data['flair_desc_bbcode_options']) ? (int) $this->data['flair_desc_bbcode_options'] : 0;

		$content_data = generate_text_for_edit($content, $uid, $options);

		return $content_data['text'];
	}

	public function get_desc_for_display()
	{
		$content = isset($this->data['flair_desc']) ? $this->data['flair_desc'] : '';
		$uid = isset($this->data['flair_desc_bbcode_uid']) ? $this->data['flair_desc_bbcode_uid'] : '';
		$bitfield = isset($this->data['flair_desc_bbcode_bitfield']) ? $this->data['flair_desc_bbcode_bitfield'] : '';
		$options = isset($this->data['flair_desc_bbcode_options']) ? (int) $this->data['flair_desc_bbcode_options'] : 0;

		return generate_text_for_display($content, $uid, $bitfield, $options);
	}

	public function set_desc($desc)
	{
		$this->config['max_post_chars'] = 0;

		$uid = $bitfield = $flags = '';
		generate_text_for_storage($desc, $uid, $bitfield, $flags, $this->is_bbcode_enabled(), $this->is_magic_url_enabled(), $this->is_smilies_enabled());

		$this->data['flair_desc'] = $desc;
		$this->data['flair_desc_bbcode_uid'] = $uid;
		$this->data['flair_desc_bbcode_bitfield'] = $bitfield;

		return $this;
	}

	public function is_bbcode_enabled()
	{
		return ($this->data['flair_desc_bbcode_options'] & OPTION_FLAG_BBCODE);
	}

	public function set_bbcode_enabled($enable)
	{
		$this->set_desc_option(OPTION_FLAG_BBCODE, $enable);

		return $this;
	}

	public function is_magic_url_enabled()
	{
		return ($this->data['flair_desc_bbcode_options'] & OPTION_FLAG_LINKS);
	}

	public function set_magic_url_enabled($enable)
	{
		$this->set_desc_option(OPTION_FLAG_LINKS, $enable);

		return $this;
	}

	public function is_smilies_enabled()
	{
		return ($this->data['flair_desc_bbcode_options'] & OPTION_FLAG_SMILIES);
	}

	public function set_smilies_enabled($enable)
	{
		$this->set_desc_option(OPTION_FLAG_SMILIES, $enable);

		return $this;
	}

	public function desc_disable_smilies()
	{
		$this->set_desc_option(OPTION_FLAG_SMILIES, true);

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
		$color = strtoupper($color);

		if ($color !== '' && !self::is_valid_color($color))
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
		$color = strtoupper($color);

		if ($color !== '' && !self::is_valid_color($color))
		{
			throw new out_of_bounds('flair_icon_color');
		}

		$this->data['flair_icon_color'] = $color;

		return $this;
	}

	public function get_font_color()
	{
		return isset($this->data['flair_font_color']) ? (string) $this->data['flair_font_color'] : '';
	}

	public function set_font_color($color)
	{
		$color = strtoupper($color);

		if ($color !== '' && !self::is_valid_color($color))
		{
			throw new out_of_bounds('flair_font_color');
		}

		$this->data['flair_font_color'] = $color;

		return $this;
	}

	public function show_on_profile()
	{
		return (bool) $this->data['flair_display_profile'];
	}

	public function set_show_on_profile($show_on_profile)
	{
		$show_on_profile = (bool) $show_on_profile;

		$this->data['flair_display_profile'] = (int) $show_on_profile;

		return $this;
	}

	public function show_on_posts()
	{
		return (bool) $this->data['flair_display_posts'];
	}

	public function set_show_on_posts($show_on_posts)
	{
		$show_on_posts = (bool) $show_on_posts;

		$this->data['flair_display_posts'] = (int) $show_on_posts;

		return $this;
	}

	/**
	 * Set an option on the description.
	 *
	 * @param int		$option
	 * @param boolean	$value
	 */
	protected function set_desc_option($option, $value)
	{
		$this->data['flair_desc_bbcode_options'] = isset($this->data['flair_desc_bbcode_options']) ? $this->data['flair_desc_bbcode_options'] : 0;

		if ($value && !($this->data['flair_desc_bbcode_options'] & $option))
		{
			$this->data['flair_desc_bbcode_options'] += $option;
		}

		if (!$value && $this->data['flair_desc_bbcode_options'] & $option)
		{
			$this->data['flair_desc_bbcode_options'] -= $option;
		}

		if (!empty($this->data['flair_desc']))
		{
			$content = $this->data['flair_desc'];

			decode_message($content, $this->data['flair_desc_bbcode_uid']);

			$this->set_desc($content);
		}
	}

	/**
	 * Check if a given string is a valid color hexadecimal value.
	 *
	 * @param string	$color	The string to check
	 *
	 * @return boolean The string is a valid color hexadecimal value
	 */
	static protected function is_valid_color($color)
	{
		return preg_match('/^[0-9A-F]{6}$/i', $color) === 1;
	}
}
